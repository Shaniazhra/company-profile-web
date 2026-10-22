<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Turns big PNG photos (no transparency) into WebP, which is usually 80-90% smaller.
 * Every database column that holds the old file path is updated to the new path, and the old file is removed.
 * PNG files with transparency (cut-outs, logos) are left alone.
 *
 *   php artisan images:to-webp --dry-run     only show what would change
 *   php artisan images:to-webp               convert (the originals are copied to storage/app/image-backup first)
 *   php artisan images:to-webp --repair      fix database values that still point to an old .png (for example inside JSON lists)
 */
class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:to-webp
        {--quality=80 : WebP quality (1-100)}
        {--min-kb=150 : Skip PNG files smaller than this}
        {--repair : Only fix the database for files that were already converted (uses the copies in storage/app/image-backup)}
        {--dry-run : Only show what would change}
        {--no-backup : Do not copy the originals first}';

    protected $description = 'Convert PNG photos without transparency in storage/app/public to WebP and update the database';

    /** Tables that never hold image paths. */
    private const SKIP_TABLES = ['migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens'];

    public function handle(): int
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            $this->error('The PHP "gd" extension with WebP support is not available. Enable extension=gd in php.ini and try again.');

            return self::FAILURE;
        }

        @ini_set('memory_limit', '1024M');

        if ($this->option('repair')) {
            return $this->repair();
        }

        $quality = min(100, max(1, (int) $this->option('quality')));
        $minSize = max(0, (int) $this->option('min-kb')) * 1024;
        $dry     = (bool) $this->option('dry-run');
        $backup  = ! $dry && ! $this->option('no-backup');

        $root       = Storage::disk('public')->path('');
        $backupRoot = storage_path('app/image-backup');
        $before = $after = $converted = $rows = 0;

        foreach (Storage::disk('public')->allFiles() as $relative) {
            if (strtolower(pathinfo($relative, PATHINFO_EXTENSION)) !== 'png') {
                continue;
            }

            $path = $root.$relative;
            $size = filesize($path);

            if ($size < $minSize) {
                continue;
            }

            $target = preg_replace('/\.png$/i', '.webp', $relative);

            if (Storage::disk('public')->exists($target)) {
                continue;
            }

            $image = @imagecreatefrompng($path);

            if (! $image) {
                $this->warn("Skipped (cannot read): {$relative}");

                continue;
            }

            imagepalettetotruecolor($image);

            if ($this->hasTransparency($image)) {
                $this->line("Kept (has transparency): {$relative}");

                continue;
            }

            $tmp = $path.'.tmp';
            imagewebp($image, $tmp, $quality);
            $new = file_exists($tmp) ? filesize($tmp) : $size;

            if ($new >= $size) {
                @unlink($tmp);
                $this->line("Kept (no gain): {$relative}");

                continue;
            }

            $before += $size;
            $after  += $new;
            $converted++;
            $this->line(sprintf('%s  %.2f MB -> %.2f MB  (%s)', $relative, $size / 1048576, $new / 1048576, $target));

            if ($dry) {
                @unlink($tmp);

                continue;
            }

            if ($backup) {
                $copy = $backupRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
                File::ensureDirectoryExists(dirname($copy));
                File::copy($path, $copy);
            }

            rename($tmp, $root.$target);
            $rows += $this->replaceReferences($relative, $target);
            @unlink($path);
        }

        $this->newLine();
        $this->info(sprintf(
            '%s %d file(s): %.1f MB -> %.1f MB.%s',
            $dry ? 'Would convert' : 'Converted',
            $converted,
            $before / 1048576,
            $after / 1048576,
            $dry ? '' : " {$rows} database value(s) updated."
        ));

        if ($backup && $converted) {
            $this->line('Originals are in storage/app/image-backup. Delete that folder when you are happy with the result.');
        }

        return self::SUCCESS;
    }

    /** True when any pixel of a small copy of the image is not fully opaque. */
    private function hasTransparency(\GdImage $image): bool
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $small = imagescale($image, min(96, $w), min(96, $h));

        if (! $small) {
            return true;
        }

        for ($x = 0; $x < imagesx($small); $x++) {
            for ($y = 0; $y < imagesy($small); $y++) {
                if (((imagecolorat($small, $x, $y) >> 24) & 0x7F) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /** Fix the database for files that were converted before: the .png is gone and the .webp exists. */
    private function repair(): int
    {
        $backupRoot = storage_path('app/image-backup');

        if (! is_dir($backupRoot)) {
            $this->error('No backup folder found at storage/app/image-backup.');

            return self::FAILURE;
        }

        $rows = 0;

        foreach (File::allFiles($backupRoot) as $file) {
            if (strtolower($file->getExtension()) !== 'png') {
                continue;
            }

            $old = str_replace('\\', '/', $file->getRelativePathname());
            $new = preg_replace('/\.png$/i', '.webp', $old);

            if (! Storage::disk('public')->exists($new) || Storage::disk('public')->exists($old)) {
                continue;
            }

            $changed = $this->replaceReferences($old, $new);
            $rows += $changed;

            if ($changed) {
                $this->line("{$old}: {$changed} value(s) updated");
            }
        }

        $this->info("Done. {$rows} database value(s) updated.");

        return self::SUCCESS;
    }

    /**
     * Replace the old path with the new one in every text and JSON column of every table, also when the path is
     * part of a longer value (JSON lists store paths with escaped slashes, like "projects\/characters\/a.png").
     * Returns how many values changed.
     */
    private function replaceReferences(string $old, string $new): int
    {
        $changed = 0;
        $pdo     = DB::connection()->getPdo();
        $grammar = DB::connection()->getQueryGrammar();

        $pairs = [[$old, $new], [str_replace('/', '\\/', $old), str_replace('/', '\\/', $new)]];

        foreach (Schema::getTables() as $table) {
            $name = $table['name'];

            if (in_array($name, self::SKIP_TABLES, true)) {
                continue;
            }

            foreach (Schema::getColumns($name) as $column) {
                if (! in_array(strtolower($column['type_name']), ['varchar', 'char', 'text', 'tinytext', 'mediumtext', 'longtext', 'json'], true)) {
                    continue;
                }

                $wrapped = $grammar->wrap($column['name']);

                foreach ($pairs as [$from, $to]) {
                    $like = '%'.addcslashes($from, '%_\\').'%';

                    $changed += DB::table($name)
                        ->whereRaw("CAST({$wrapped} AS CHAR) LIKE ?", [$like])
                        ->update([$column['name'] => DB::raw('REPLACE('.$wrapped.', '.$pdo->quote($from).', '.$pdo->quote($to).')')]);
                }
            }
        }

        return $changed;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Makes the uploaded photos lighter. Each file keeps its own name and type (JPG stays JPG, PNG stays PNG, transparency is kept),
 * so nothing saved in the database has to change. Only the pixel size and the file size go down.
 *
 *   php artisan images:optimize --dry-run      only show what would change
 *   php artisan images:optimize                shrink the files (the originals are copied to storage/app/image-backup first)
 */
class OptimizeImages extends Command
{
    protected $signature = 'images:optimize
        {--max=1600 : Longest side in pixels}
        {--min-kb=300 : Skip files that are already smaller than this and not larger than --max}
        {--quality=82 : JPEG and WebP quality (1-100)}
        {--dry-run : Only show what would change}
        {--no-backup : Do not copy the originals first}';

    protected $description = 'Shrink the uploaded photos in storage/app/public to a web-friendly size';

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('The PHP "gd" extension is not enabled. Remove the ";" before extension=gd in php.ini, then try again.');

            return self::FAILURE;
        }

        @ini_set('memory_limit', '1024M');

        $max     = max(400, (int) $this->option('max'));
        $minSize = max(0, (int) $this->option('min-kb')) * 1024;
        $quality = min(100, max(1, (int) $this->option('quality')));
        $dry     = (bool) $this->option('dry-run');
        $backup  = ! $dry && ! $this->option('no-backup');

        $root       = Storage::disk('public')->path('');
        $backupRoot = storage_path('app/image-backup');
        $before = $after = $changed = 0;

        foreach (Storage::disk('public')->allFiles() as $relative) {
            $ext = strtolower(pathinfo($relative, PATHINFO_EXTENSION));

            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $path = $root.$relative;
            $size = filesize($path);
            $info = @getimagesize($path);

            if (! $info) {
                continue;
            }

            [$w, $h] = $info;

            if ($size < $minSize && max($w, $h) <= $max) {
                continue;
            }

            $scale = min(1, $max / max($w, $h));
            $nw    = (int) round($w * $scale);
            $nh    = (int) round($h * $scale);

            $src = match ($ext) {
                'png'  => @imagecreatefrompng($path),
                'webp' => @imagecreatefromwebp($path),
                default => @imagecreatefromjpeg($path),
            };

            if (! $src) {
                $this->warn("Skipped (cannot read): {$relative}");

                continue;
            }

            // Phone photos store their rotation in the file info. Apply it, because saving again drops that info.
            if (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('exif_read_data')) {
                $orientation = @exif_read_data($path)['Orientation'] ?? 1;
                $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;

                if ($angle) {
                    $rotated = imagerotate($src, $angle, 0);

                    if ($rotated) {
                        $src = $rotated;
                        [$w, $h] = [imagesx($src), imagesy($src)];
                        $nw = (int) round($w * $scale);
                        $nh = (int) round($h * $scale);
                    }
                }
            }

            $dst = $scale < 1 ? imagescale($src, $nw, $nh, IMG_BICUBIC) : $src;

            $tmp = $path.'.tmp';

            match ($ext) {
                'png'  => (function () use ($dst, $tmp) {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                    imagepng($dst, $tmp, 9);
                })(),
                'webp' => imagewebp($dst, $tmp, $quality),
                default => (function () use ($dst, $tmp, $quality) {
                    imageinterlace($dst, true);
                    imagejpeg($dst, $tmp, $quality);
                })(),
            };

            $new = file_exists($tmp) ? filesize($tmp) : $size;

            if ($new >= $size) {
                @unlink($tmp);
                $this->line("Kept (no gain): {$relative}");

                continue;
            }

            $before += $size;
            $after  += $new;
            $changed++;
            $this->line(sprintf('%s  %.2f MB -> %.2f MB  (%dx%d -> %dx%d)', $relative, $size / 1048576, $new / 1048576, $w, $h, $nw, $nh));

            if ($dry) {
                @unlink($tmp);

                continue;
            }

            if ($backup) {
                $copy = $backupRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
                File::ensureDirectoryExists(dirname($copy));
                File::copy($path, $copy);
            }

            rename($tmp, $path);
        }

        $this->newLine();
        $this->info(sprintf(
            '%s %d file(s): %.1f MB -> %.1f MB.',
            $dry ? 'Would shrink' : 'Shrunk',
            $changed,
            $before / 1048576,
            $after / 1048576
        ));

        if ($backup && $changed) {
            $this->line('Originals are in storage/app/image-backup. Delete that folder when you are happy with the result.');
        }

        return self::SUCCESS;
    }
}
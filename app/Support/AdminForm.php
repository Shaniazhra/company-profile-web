<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminForm
{
    /** Languages available in the admin forms. Add one here to support another language. */
    public const LOCALES = ['en' => 'English'];

    public static function imageRules(): array
    {
        return ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'];
    }

    /** Aturan validasi untuk satu field multi-bahasa. The first language (en) is required when $required is true. */
    public static function translationRules(string $field, bool $required = false, int $max = 2000): array
    {
        $rules = [$field => [$required ? 'required' : 'nullable', 'array']];

        foreach (array_keys(self::LOCALES) as $locale) {
            $rules["$field.$locale"] = [
                $required && $locale === array_key_first(self::LOCALES) ? 'required' : 'nullable',
                'string',
                "max:$max",
            ];
        }

        return $rules;
    }

    /** Buang bahasa yang dikosongkan. */
    public static function clean(?array $values): array
    {
        return array_filter($values ?? [], fn ($value) => filled($value));
    }

    /**
     * Simpan/hapus/ganti gambar. Field "remove_{nama}" = hapus gambar lama.
     * Mengembalikan path terbaru (atau null).
     */
    public static function syncImage(Request $request, ?string $current, string $field, string $dir): ?string
    {
        $path = $current;

        if ($request->boolean("remove_$field") && $path) {
            Storage::disk('public')->delete($path);
            $path = null;
        }

        if ($request->hasFile($field)) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = self::storeOptimized($request->file($field), $dir);
        }

        return $path;
    }

    /**
     * Store an uploaded photo on the public disk, made lighter for the web:
     * the longest side is at most $max pixels. The file type stays the same (a PNG stays a PNG, so transparency is kept).
     * If the photo cannot be processed (no gd, unreadable file), it is stored as it is.
     * Returns the stored path, like UploadedFile::store().
     */
    public static function storeOptimized(UploadedFile $file, string $dir, int $max = 1600, int $quality = 82): string
    {
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());

        if (! extension_loaded('gd') || ! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $file->store($dir, 'public');
        }

        @ini_set('memory_limit', '1024M');

        $source = $file->getRealPath();
        $info   = @getimagesize($source);

        if (! $info) {
            return $file->store($dir, 'public');
        }

        [$w, $h] = $info;

        $image = match ($ext) {
            'png'   => @imagecreatefrompng($source),
            'webp'  => @imagecreatefromwebp($source),
            default => @imagecreatefromjpeg($source),
        };

        if (! $image) {
            return $file->store($dir, 'public');
        }

        // Phone photos keep their rotation in the file info, which is lost when the file is saved again. Apply it first.
        if (in_array($ext, ['jpg', 'jpeg'], true) && function_exists('exif_read_data')) {
            $angle = [3 => 180, 6 => -90, 8 => 90][@exif_read_data($source)['Orientation'] ?? 1] ?? 0;

            if ($angle && ($rotated = imagerotate($image, $angle, 0))) {
                $image = $rotated;
                [$w, $h] = [imagesx($image), imagesy($image)];
            }
        }

        $scale = min(1, $max / max($w, $h));

        if ($scale < 1) {
            $image = imagescale($image, (int) round($w * $scale), (int) round($h * $scale), IMG_BICUBIC);
        }

        ob_start();
        match ($ext) {
            'png' => (function () use ($image) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
                imagepng($image, null, 9);
            })(),
            'webp' => imagewebp($image, null, $quality),
            default => (function () use ($image, $quality) {
                imageinterlace($image, true);
                imagejpeg($image, null, $quality);
            })(),
        };
        $data = (string) ob_get_clean();

        // Keep the original when this did not make it smaller (for example a small PNG).
        if ($data === '' || ($scale >= 1 && strlen($data) >= $file->getSize())) {
            return $file->store($dir, 'public');
        }

        $path = trim($dir, '/').'/'.Str::random(40).'.'.($ext === 'jpeg' ? 'jpg' : $ext);
        Storage::disk('public')->put($path, $data);

        return $path;
    }
}
<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $path = $request->file($field)->store($dir, 'public');
        }

        return $path;
    }
}
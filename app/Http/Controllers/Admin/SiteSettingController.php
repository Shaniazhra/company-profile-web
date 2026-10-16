<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\AdminForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    /** Kolom teks yang punya versi ID/EN (harus sama dengan $translatable di model). Nilai = batas karakter, null = bawaan. */
    private const TEXT_FIELDS = [
        'tagline' => 200,
        'heart_statement' => null,
        'impact_statement' => null,
        'about_text' => 5000,
    ];

    /** Kolom gambar. max dalam KB. */
    private const IMAGES = [
        'logo' => ['label' => 'Logo', 'max' => 2048],
        'hero_image' => ['label' => 'Gambar hero', 'max' => 5120],
    ];

    public function edit()
    {
        return view('admin.site.edit', [
            'site' => SiteSetting::first() ?? new SiteSetting,
        ]);
    }

    /** Simpan nama situs saja. */
    public function updateName(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
        ]);

        $site = SiteSetting::current();
        $site->site_name = $data['site_name'];
        $site->save();

        return $this->saved('name', 'Site name saved.');
    }

    /** Simpan satu teks (dua bahasa) saja. */
    public function updateText(Request $request, string $field): RedirectResponse
    {
        abort_unless(array_key_exists($field, self::TEXT_FIELDS), 404);

        $max = self::TEXT_FIELDS[$field];
        $rules = $max
            ? AdminForm::translationRules($field, max: $max)
            : AdminForm::translationRules($field);

        $data = $request->validate($rules);

        $site = SiteSetting::current();
        $site->setTranslations($field, AdminForm::clean($data[$field] ?? []));
        $site->save();

        return $this->saved($field, 'Text saved.');
    }

    /** Unggah logo atau gambar hero. File lama otomatis dihapus. */
    public function uploadImage(Request $request, string $field): RedirectResponse
    {
        abort_unless(isset(self::IMAGES[$field]), 404);

        $info = self::IMAGES[$field];
        $limitMb = $info['max'] / 1024;

        $request->validate([
            $field => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:'.$info['max']],
        ], [
            "$field.required" => 'Pilih file gambar terlebih dahulu.',
            "$field.image" => 'File harus berupa gambar.',
            "$field.mimes" => 'The file must be a PNG, JPG, or WebP image.',
            "$field.max" => "Ukuran file maksimal {$limitMb} MB.",
            "$field.uploaded" => 'Upload failed. The file may exceed the server limit (upload_max_filesize in php.ini).',
        ]);

        $site = SiteSetting::current();

        $this->deleteFile($site->{$field});
        $site->{$field} = $request->file($field)->store('site', 'public');
        $site->save();

        return $this->saved($field, $info['label'].' uploaded.');
    }

    /** Hapus logo atau gambar hero. */
    public function destroyImage(string $field): RedirectResponse
    {
        abort_unless(isset(self::IMAGES[$field]), 404);

        $site = SiteSetting::current();

        $this->deleteFile($site->{$field});
        $site->{$field} = null;
        $site->save();

        return $this->saved($field, self::IMAGES[$field]['label'].' removed.');
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /** Kembali ke kartu yang baru disimpan, dengan pesan sukses di kartu itu. */
    private function saved(string $section, string $message): RedirectResponse
    {
        return redirect()
            ->to(route('admin.site.edit').'#sec-'.$section)
            ->with('status', $message)
            ->with('section', $section);
    }
}
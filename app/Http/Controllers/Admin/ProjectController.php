<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        return view('admin.projects.index', [
            'projects' => Project::query()->ordered()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('admin.projects.create', [
            'project' => new Project(['is_published' => false, 'sort_order' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $project = new Project;
        $project->slug = $this->uniqueSlug($data['title']['id']);
        $this->persist($project, $request, $data);

        return redirect()->route('admin.projects.index')->with('status', 'Proyek berhasil ditambahkan.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', ['project' => $project]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validated($request);

        // Slug tetap setelah dibuat; hanya diisi kalau proyek lama belum punya.
        if (blank($project->slug)) {
            $project->slug = $this->uniqueSlug($data['title']['id'], $project->id);
        }

        $this->persist($project, $request, $data);

        return redirect()->route('admin.projects.index')->with('status', 'Perubahan proyek disimpan.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $files = array_merge(
            [$project->image, $project->logo],
            $this->characterPhotos($project->characters ?? [])
        );

        $project->delete();

        foreach ($files as $file) {
            $this->deleteFile($file);
        }

        return redirect()->route('admin.projects.index')->with('status', 'Proyek dihapus.');
    }

    /** Ganti status tayang/draf dari daftar. */
    public function togglePublish(Project $project): RedirectResponse
    {
        $project->is_published = ! $project->is_published;
        $project->save();

        return back()->with('status', $project->is_published ? 'Proyek ditayangkan.' : 'Proyek diubah jadi draf.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title.id'     => ['required', 'string', 'max:150'],
            'title.en'     => ['nullable', 'string', 'max:150'],
            'tag.id'       => ['nullable', 'string', 'max:80'],
            'tag.en'       => ['nullable', 'string', 'max:80'],
            'synopsis.id'  => ['nullable', 'string', 'max:5000'],
            'synopsis.en'  => ['nullable', 'string', 'max:5000'],
            'youtube_url'  => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\/(www\.|m\.)?(youtube\.com|youtu\.be)\//i'],
            'release_year' => ['nullable', 'integer', 'between:1990,2100'],
            'sort_order'   => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'logo'         => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'characters'                  => ['nullable', 'array', 'max:30'],
            'characters.*.name'           => ['nullable', 'string', 'max:80'],
            'characters.*.role'           => ['nullable', 'string', 'max:120'],
            'characters.*.photo'          => ['nullable', 'string', 'max:255'],
            'characters.*.photo_file'     => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ], [
            'title.id.required' => 'Judul (Indonesia) wajib diisi.',
            'youtube_url.url'   => 'Link YouTube tidak valid.',
            'youtube_url.regex' => 'Link harus dari youtube.com atau youtu.be.',
            'image.image'       => 'File harus berupa gambar.',
            'image.mimes'       => 'Format gambar harus PNG, JPG, atau WebP.',
            'image.max'         => 'Ukuran gambar maksimal 5 MB.',
            'image.uploaded'    => 'Unggahan gagal. Ukuran file kemungkinan melebihi batas server (upload_max_filesize di php.ini).',
            'logo.image'        => 'Logo harus berupa gambar.',
            'logo.mimes'        => 'Format logo harus PNG, JPG, atau WebP.',
            'logo.max'          => 'Ukuran logo maksimal 2 MB.',
            'logo.uploaded'     => 'Unggahan logo gagal. Ukuran file kemungkinan melebihi batas server.',
            'characters.*.photo_file.image'    => 'Foto karakter harus berupa gambar.',
            'characters.*.photo_file.mimes'    => 'Format foto karakter harus PNG, JPG, atau WebP.',
            'characters.*.photo_file.max'      => 'Ukuran foto karakter maksimal 2 MB.',
            'characters.*.photo_file.uploaded' => 'Unggahan foto karakter gagal. Ukuran file kemungkinan melebihi batas server.',
        ], [
            'title.en' => 'judul (English)', 'tag.id' => 'tag (Indonesia)', 'tag.en' => 'tag (English)',
            'synopsis.id' => 'sinopsis (Indonesia)', 'synopsis.en' => 'sinopsis (English)',
            'release_year' => 'tahun rilis', 'sort_order' => 'urutan',
            'characters.*.name' => 'nama karakter', 'characters.*.role' => 'peran karakter',
        ]);
    }

    private function persist(Project $project, Request $request, array $data): void
    {
        foreach (['title', 'tag', 'synopsis'] as $field) {
            $project->setTranslations($field, array_filter($data[$field] ?? [], fn ($v) => filled($v)));
        }

        $project->youtube_url  = $data['youtube_url'] ?? null;
        $project->release_year = $data['release_year'] ?? null;
        $project->sort_order   = $data['sort_order'] ?? 0;
        $project->is_published = $request->boolean('is_published');

        // Karakter + fotonya
        $oldPhotos  = $this->characterPhotos($project->characters ?? []);
        $characters = $this->buildCharacters($request, $oldPhotos, $data['characters'] ?? []);
        $project->characters = $characters;

        // Gambar proyek
        $oldImage = $project->image;

        if ($request->hasFile('image')) {
            $project->image = $request->file('image')->store('projects', 'public');
        } elseif ($request->boolean('remove_image')) {
            $project->image = null;
        }

        // Logo judul
        $oldLogo = $project->logo;

        if ($request->hasFile('logo')) {
            $project->logo = $request->file('logo')->store('projects/logos', 'public');
        } elseif ($request->boolean('remove_logo')) {
            $project->logo = null;
        }

        $project->save();

        // Bersihkan file yang tidak dipakai lagi
        if ($oldImage && $oldImage !== $project->image) {
            $this->deleteFile($oldImage);
        }

        if ($oldLogo && $oldLogo !== $project->logo) {
            $this->deleteFile($oldLogo);
        }

        $keptPhotos = $this->characterPhotos($characters);
        foreach (array_diff($oldPhotos, $keptPhotos) as $orphan) {
            $this->deleteFile($orphan);
        }
    }

    /**
     * Susun daftar karakter. Baris tanpa nama dibuang.
     * Path foto lama dari browser hanya diterima kalau memang sudah tercatat di proyek ini.
     */
    private function buildCharacters(Request $request, array $oldPhotos, array $rows): array
    {
        $result = [];

        foreach ($rows as $i => $row) {
            if (blank($row['name'] ?? null)) {
                continue;
            }

            $photo = $row['photo'] ?? null;
            $photo = in_array($photo, $oldPhotos, true) ? $photo : null;

            if ($request->hasFile("characters.$i.photo_file")) {
                $photo = $request->file("characters.$i.photo_file")->store('projects/characters', 'public');
            }

            $result[] = [
                'name'  => trim($row['name']),
                'role'  => filled($row['role'] ?? null) ? trim($row['role']) : null,
                'photo' => $photo,
            ];
        }

        return $result;
    }

    /** Ambil semua path foto dari daftar karakter. */
    private function characterPhotos(array $characters): array
    {
        return collect($characters)->pluck('photo')->filter()->values()->all();
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'proyek';
        $slug = $base;
        $i = 2;

        while (Project::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
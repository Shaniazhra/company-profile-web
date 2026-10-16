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
        $project->slug = $this->uniqueSlug($data['title']['en']);
        $this->persist($project, $request, $data);

        return redirect()->route('admin.projects.index')->with('status', 'Project added.');
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
            $project->slug = $this->uniqueSlug($data['title']['en'], $project->id);
        }

        $this->persist($project, $request, $data);

        return redirect()->route('admin.projects.index')->with('status', 'Project changes saved.');
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

        return redirect()->route('admin.projects.index')->with('status', 'Project deleted.');
    }

    /** Ganti status tayang/draf dari daftar. */
    public function togglePublish(Project $project): RedirectResponse
    {
        $project->is_published = ! $project->is_published;
        $project->save();

        return back()->with('status', $project->is_published ? 'Project published.' : 'Project set to draft.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title.id'     => ['nullable', 'string', 'max:150'],
            'title.en'     => ['required', 'string', 'max:150'],
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
            'title.en.required' => 'The title (English) is required.',
            'youtube_url.url'   => 'The YouTube link is not valid.',
            'youtube_url.regex' => 'The link must be from youtube.com or youtu.be.',
            'image.image'       => 'The file must be an image.',
            'image.mimes'       => 'The image must be a PNG, JPG, or WebP file.',
            'image.max'         => 'The image may not be larger than 5 MB.',
            'image.uploaded'    => 'Upload failed. The file may exceed the server limit (upload_max_filesize in php.ini).',
            'logo.image'        => 'The logo must be an image.',
            'logo.mimes'        => 'The logo must be a PNG, JPG, or WebP file.',
            'logo.max'          => 'Ukuran logo maksimal 2 MB.',
            'logo.uploaded'     => 'Logo upload failed. The file may exceed the server limit.',
            'characters.*.photo_file.image'    => 'The character photo must be an image.',
            'characters.*.photo_file.mimes'    => 'The character photo must be a PNG, JPG, or WebP file.',
            'characters.*.photo_file.max'      => 'The character photo may not be larger than 2 MB.',
            'characters.*.photo_file.uploaded' => 'Character photo upload failed. The file may exceed the server limit.',
        ], [
            'title.en' => 'title (English)', 'tag.id' => 'tag (Indonesian)', 'tag.en' => 'tag (English)',
            'synopsis.id' => 'synopsis (Indonesian)', 'synopsis.en' => 'synopsis (English)',
            'release_year' => 'release year', 'sort_order' => 'order',
            'characters.*.name' => 'character name', 'characters.*.role' => 'character role',
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
        $base = Str::slug($title) ?: 'project';
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
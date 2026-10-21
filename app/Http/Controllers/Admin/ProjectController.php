<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        $project->slug = $this->uniqueSlug($this->slugFromLogo($request));
        $this->persist($project, $request, $data);

        return redirect()->route('admin.projects.index')->with('status', 'Project added.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', ['project' => $project]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validated($request, $project);

        // The slug stays the same once created; it is only filled for an old project that has none.
        if (blank($project->slug)) {
            $project->slug = $this->uniqueSlug($this->slugFromLogo($request), $project->id);
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

    /** Save the hero photo of the public Projects page: the file (optional when one is already saved), its position, and its zoom. */
    public function updateHero(Request $request): RedirectResponse
    {
        $site = SiteSetting::current();

        $request->validate([
            'projects_image' => [Rule::requiredIf(blank($site->projects_image)), 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'focus_x'        => ['nullable', 'numeric', 'between:0,100'],
            'focus_y'        => ['nullable', 'numeric', 'between:0,100'],
            'zoom'           => ['nullable', 'numeric', 'between:1,3'],
        ], [
            'projects_image.required' => 'Choose a photo first.',
            'projects_image.image'    => 'The file must be an image.',
            'projects_image.mimes'    => 'The photo must be a PNG, JPG, or WebP file.',
            'projects_image.max'      => 'The photo may not be larger than 5 MB.',
            'projects_image.uploaded' => 'Upload failed. The file may exceed the server limit (upload_max_filesize in php.ini).',
        ]);

        $old = null;

        if ($request->hasFile('projects_image')) {
            $old = $site->projects_image;
            $site->projects_image = $request->file('projects_image')->store('site', 'public');
        }

        $site->projects_image_x    = (float) $request->input('focus_x', 50);
        $site->projects_image_y    = (float) $request->input('focus_y', 50);
        $site->projects_image_zoom = (float) $request->input('zoom', 1);
        $site->save();

        $this->deleteFile($old);

        return redirect()->route('admin.projects.index')->with('status', 'Hero photo saved.');
    }

    /** Save the typed titles of the projects in the list, all at once. */
    public function updateTitles(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'titles'   => ['required', 'array'],
            'titles.*' => ['required', 'string', 'max:120'],
        ], [
            'titles.*.required' => 'Every project needs a title.',
            'titles.*.max'      => 'A title may not be longer than 120 characters.',
        ]);

        foreach (Project::whereIn('id', array_keys($data['titles']))->get() as $project) {
            $project->title = trim($data['titles'][$project->id]);
            $project->save();
        }

        return redirect()->route('admin.projects.index')->with('status', 'Titles saved.');
    }

    /** Remove the hero photo of the public Projects page. */
    public function destroyHero(): RedirectResponse
    {
        $site = SiteSetting::current();
        $old  = $site->projects_image;

        $site->projects_image = null;
        $site->save();

        $this->deleteFile($old);

        return redirect()->route('admin.projects.index')->with('status', 'Hero photo removed.');
    }

    /** Switch between published and draft from the list. */
    public function togglePublish(Project $project): RedirectResponse
    {
        $project->is_published = ! $project->is_published;
        $project->save();

        return back()->with('status', $project->is_published ? 'Project published.' : 'Project set to draft.');
    }

    private function validated(Request $request, ?Project $project = null): array
    {
        return $request->validate([
            // The title text is shown on the Projects pages. The home page shows the title logo instead.
            'title'        => ['required', 'string', 'max:120'],
            'tag.en'       => ['nullable', 'string', 'max:80'],
            'synopsis.en'  => ['nullable', 'string', 'max:5000'],
            'youtube_url'  => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\/(www\.|m\.)?(youtube\.com|youtu\.be)\//i'],
            'release_year' => ['nullable', 'integer', 'between:1990,2100'],
            'sort_order'   => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            // The logo is the project title, so a project must always have one.
            'logo'         => [Rule::requiredIf(blank($project?->logo)), 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'characters'               => ['nullable', 'array', 'max:30'],
            'characters.*.name'        => ['nullable', 'string', 'max:80'],
            'characters.*.role'        => ['nullable', 'string', 'max:120'],
            'characters.*.photo'       => ['nullable', 'string', 'max:255'],
            'characters.*.photo_file'  => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ], [
            'title.required'    => 'Enter the project title.',
            'logo.required'     => 'Upload the title logo.',
            'youtube_url.url'   => 'The YouTube link is not valid.',
            'youtube_url.regex' => 'The link must be from youtube.com or youtu.be.',
            'image.image'       => 'The file must be an image.',
            'image.mimes'       => 'The image must be a PNG, JPG, or WebP file.',
            'image.max'         => 'The image may not be larger than 5 MB.',
            'image.uploaded'    => 'Upload failed. The file may exceed the server limit (upload_max_filesize in php.ini).',
            'logo.image'        => 'The logo must be an image.',
            'logo.mimes'        => 'The logo must be a PNG, JPG, or WebP file.',
            'logo.max'          => 'The logo may not be larger than 2 MB.',
            'logo.uploaded'     => 'Logo upload failed. The file may exceed the server limit.',
            'characters.*.photo_file.image'    => 'The character photo must be an image.',
            'characters.*.photo_file.mimes'    => 'The character photo must be a PNG, JPG, or WebP file.',
            'characters.*.photo_file.max'      => 'The character photo may not be larger than 2 MB.',
            'characters.*.photo_file.uploaded' => 'Character photo upload failed. The file may exceed the server limit.',
        ], [
            'tag.en' => 'tag', 'synopsis.en' => 'synopsis',
            'release_year' => 'release year', 'sort_order' => 'order',
            'characters.*.name' => 'character name', 'characters.*.role' => 'character role',
        ]);
    }

    private function persist(Project $project, Request $request, array $data): void
    {
        foreach (['tag', 'synopsis'] as $field) {
            $project->setTranslations($field, array_filter($data[$field] ?? [], fn ($v) => filled($v)));
        }

        $project->title        = trim($data['title']);
        $project->youtube_url  = $data['youtube_url'] ?? null;
        $project->release_year = $data['release_year'] ?? null;
        if (array_key_exists('sort_order', $data)) {
        $project->sort_order = $data['sort_order'] ?? 0;
        }
        $project->is_published = $request->boolean('is_published');

        // Characters and their photos
        $oldPhotos  = $this->characterPhotos($project->characters ?? []);
        $characters = $this->buildCharacters($request, $oldPhotos, $data['characters'] ?? []);
        $project->characters = $characters;

        // Project image
        $oldImage = $project->image;

        if ($request->hasFile('image')) {
            $project->image = $request->file('image')->store('projects', 'public');
        } elseif ($request->boolean('remove_image')) {
            $project->image = null;
        }

        // Title logo
        $oldLogo = $project->logo;

        if ($request->hasFile('logo')) {
            $project->logo = $request->file('logo')->store('projects/logos', 'public');
        }

        $project->save();

        // Clean up files that are no longer used
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
     * Build the character list. Rows without a name are dropped.
     * An old photo path from the browser is only accepted if it is already saved on this project.
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

    /** Get every photo path from the character list. */
    private function characterPhotos(array $characters): array
    {
        return collect($characters)->pluck('photo')->filter()->values()->all();
    }

    /** The slug comes from the logo file name, without a trailing "logo" ("ibra-logo.png" becomes "ibra"). */
    private function slugFromLogo(Request $request): string
    {
        $file = $request->file('logo');
        $base = $file ? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) : '';
        $base = preg_replace('/[\s_-]*logo$/i', '', $base);

        return $base !== '' ? $base : 'project';
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
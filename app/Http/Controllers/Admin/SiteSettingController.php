<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPhoto;
use App\Models\SiteSetting;
use App\Support\AdminForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    /** Text columns that have an ID/EN version (must match $translatable in the model). Value = max characters, null = default. */
    private const TEXT_FIELDS = [
        'tagline' => 200,
        'heart_statement' => null,
        'impact_statement' => null,
        'about_text' => 5000,
        'story' => 5000,
        'vision' => 1000,
        'mission' => 2000,
        'bts_title' => 160,
        'bts_text' => 1000,
        'gallery_title' => 160,
        'gallery_text' => 1000,
        'cta_title' => 160,
        'about_label' => 80,
        'about_title' => 160,
        'story_title' => 160,
        'cta_label' => 60,
        'cta_text' => 500,
    ];

    /** Image columns. max is in KB. */
    private const IMAGES = [
        'logo' => ['label' => 'Logo', 'max' => 2048],
        'hero_image' => ['label' => 'Hero image', 'max' => 5120],
        'about_image' => ['label' => 'About hero photo', 'max' => 5120],
        'about_image_2' => ['label' => 'Story photo 1', 'max' => 5120],
        'about_image_3' => ['label' => 'Story photo 2', 'max' => 5120],
        'about_image_4' => ['label' => 'Wide photo', 'max' => 5120],
        'bts_poster' => ['label' => 'Video poster', 'max' => 5120],
    ];

    /** Sections of the About page that can be shown or hidden. */
    private const SECTIONS = [
        'bts' => ['column' => 'bts_enabled', 'label' => 'Behind the scenes video'],
        'gallery' => ['column' => 'gallery_enabled', 'label' => 'Photo gallery'],
    ];

    /** Largest video in KB. The PHP limits upload_max_filesize and post_max_size must be at least this big. */
    private const VIDEO_MAX_KB = 51200;

    public function edit()
    {
        return view('admin.site.edit', [
            'site' => SiteSetting::first() ?? new SiteSetting,
            'galleryPhotos' => AboutPhoto::query()->ordered()->get(),
        ]);
    }

    /** Save the site name only. */
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

    /** Save one text (both languages) only. */
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

    /** Save the focus point of the home hero image (0 to 100 on each axis). The original photo is not changed. */
    public function updateHeroFocus(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero_focus_x' => ['required', 'integer', 'between:0,100'],
            'hero_focus_y' => ['required', 'integer', 'between:0,100'],
        ]);

        $site = SiteSetting::current();
        $site->hero_focus_x = $data['hero_focus_x'];
        $site->hero_focus_y = $data['hero_focus_y'];
        $site->save();

        return $this->saved('hero_focus', 'Hero position saved.');
    }

    /** Upload a site image. The previous file is deleted only after the new one is saved. */
    public function uploadImage(Request $request, string $field): RedirectResponse
    {
        abort_unless(isset(self::IMAGES[$field]), 404);

        $info = self::IMAGES[$field];
        $limitMb = $info['max'] / 1024;

        $request->validate([
            $field => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:'.$info['max']],
        ], [
            "$field.required" => 'Choose an image file first.',
            "$field.image" => 'The file must be an image.',
            "$field.mimes" => 'The file must be a PNG, JPG, or WebP image.',
            "$field.max" => "The file must not be larger than {$limitMb} MB.",
            "$field.uploaded" => 'Upload failed. The file may exceed the server limit (upload_max_filesize in php.ini).',
        ]);

        $site = SiteSetting::current();
        $old = $site->{$field};

        // Photos are made lighter (WebP, at most 2400 px wide). The logo is stored as it is, because it is also used as the site icon.
        $site->{$field} = $field === 'logo'
            ? $request->file($field)->store('site', 'public')
            : AdminForm::storeOptimized($request->file($field), 'site', 2400);

        // A new hero photo starts centered again.
        if ($field === 'hero_image') {
            $site->hero_focus_x = 50;
            $site->hero_focus_y = 50;
        }

        $this->forgetFocus($site, $field); // a new photo starts centered and not zoomed
        $site->save();

        $this->deleteFile($old);

        return $this->saved($field, $info['label'].' uploaded.');
    }

    /** Remove a site image. */
    public function destroyImage(string $field): RedirectResponse
    {
        abort_unless(isset(self::IMAGES[$field]), 404);

        $site = SiteSetting::current();
        $old = $site->{$field};

        $site->{$field} = null;

        if ($field === 'hero_image') {
            $site->hero_focus_x = 50;
            $site->hero_focus_y = 50;
        }

        $this->forgetFocus($site, $field);
        $site->save();

        $this->deleteFile($old);

        return $this->saved($field, self::IMAGES[$field]['label'].' removed.');
    }

    /** Save how a photo is positioned in its frame: x and y in percent (0 to 100), z is the zoom (1 to 3). */
    public function updateFocus(Request $request, string $field): RedirectResponse
    {
        abort_unless(isset(self::IMAGES[$field]), 404);

        $data = $request->validate([
            'x' => ['required', 'numeric', 'between:0,100'],
            'y' => ['required', 'numeric', 'between:0,100'],
            'z' => ['required', 'numeric', 'between:1,3'],
        ]);

        $site = SiteSetting::current();
        $all = $site->image_focus ?? [];

        $focus = [
            'x' => round((float) $data['x'], 1),
            'y' => round((float) $data['y'], 1),
            'z' => round((float) $data['z'], 2),
        ];

        // The default position is not stored.
        if ($focus === ['x' => 50.0, 'y' => 50.0, 'z' => 1.0]) {
            unset($all[$field]);
        } else {
            $all[$field] = $focus;
        }

        $site->image_focus = $all ?: null;
        $site->save();

        return $this->saved($field, 'Photo position saved.');
    }

    /** Upload the behind-the-scenes video (MP4 or WebM). The old video is deleted only after the new one is saved. */
    public function uploadVideo(Request $request): RedirectResponse
    {
        $limitMb = self::VIDEO_MAX_KB / 1024;

        $request->validate([
            'bts_video' => ['required', 'file', 'mimes:mp4,webm', 'mimetypes:video/mp4,video/webm', 'max:'.self::VIDEO_MAX_KB],
        ], [
            'bts_video.required' => 'Choose a video file first.',
            'bts_video.file' => 'The video must be a file.',
            'bts_video.mimes' => 'The video must be an MP4 or WebM file.',
            'bts_video.mimetypes' => 'The video must be an MP4 or WebM file.',
            'bts_video.max' => "The video must not be larger than {$limitMb} MB.",
            'bts_video.uploaded' => 'Upload failed. The video is probably larger than the server limit (upload_max_filesize and post_max_size in php.ini).',
        ]);

        $site = SiteSetting::current();
        $old = $site->bts_video;

        $site->bts_video = $request->file('bts_video')->store('site/video', 'public');
        $site->save();

        $this->deleteFile($old);

        return $this->saved('bts', 'Video uploaded.');
    }

    /** Remove the behind-the-scenes video. */
    public function destroyVideo(): RedirectResponse
    {
        $site = SiteSetting::current();
        $old = $site->bts_video;

        $site->bts_video = null;
        $site->save();

        $this->deleteFile($old);

        return $this->saved('bts', 'Video removed.');
    }

    /** Show or hide a whole section of the About page. */
    public function toggleSection(string $section): RedirectResponse
    {
        abort_unless(isset(self::SECTIONS[$section]), 404);

        $info = self::SECTIONS[$section];
        $site = SiteSetting::current();

        $site->{$info['column']} = ! $site->{$info['column']};
        $site->save();

        return $this->saved($section, $info['label'].($site->{$info['column']} ? ' is now shown.' : ' is now hidden.'));
    }

    /** Drop the saved position of one photo (the caller saves the model). */
    private function forgetFocus(SiteSetting $site, string $field): void
    {
        $all = $site->image_focus ?? [];
        unset($all[$field]);
        $site->image_focus = $all ?: null;
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /** Go back to the card that was just saved, with a success message on that card. */
    private function saved(string $section, string $message): RedirectResponse
    {
        return redirect()
            ->to(route('admin.site.edit').'#sec-'.$section)
            ->with('status', $message)
            ->with('section', $section);
    }
}
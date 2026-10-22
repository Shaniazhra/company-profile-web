<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPhoto;
use App\Support\AdminForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/** Gallery photos on the About page. Managed from the "Photo gallery" card in Site Settings. */
class AboutPhotoController extends Controller
{
    /** Largest photo in KB and most photos per upload. */
    private const MAX_KB = 5120;
    private const MAX_FILES = 20;

    /** Add one or more photos. They go to the end of the list. */
    public function store(Request $request): RedirectResponse
    {
        $limitMb = self::MAX_KB / 1024;

        $data = $request->validateWithBag('gallery', [
            'photos' => ['required', 'array', 'max:'.self::MAX_FILES],
            'photos.*' => ['image', 'mimes:png,jpg,jpeg,webp', 'max:'.self::MAX_KB],
            'alt' => ['nullable', 'string', 'max:160'],
        ], [
            'photos.required' => 'Choose at least one photo.',
            'photos.max' => 'You can add up to '.self::MAX_FILES.' photos at a time.',
            'photos.*.image' => 'Every file must be an image.',
            'photos.*.mimes' => 'Every photo must be a PNG, JPG, or WebP image.',
            'photos.*.max' => "Every photo must not be larger than {$limitMb} MB.",
            'photos.*.uploaded' => 'Upload failed. A file may exceed the server limit (upload_max_filesize and post_max_size in php.ini).',
        ]);

        $stored = [];

        try {
            DB::transaction(function () use ($request, $data, &$stored) {
                $order = (int) AboutPhoto::max('sort_order');

                foreach ($request->file('photos') as $file) {
                    $path = AdminForm::storeOptimized($file, 'about', 1600);
                    $stored[] = $path;

                    AboutPhoto::create([
                        'path' => $path,
                        'alt' => $data['alt'] ?? null,
                        'sort_order' => ++$order,
                        'is_active' => true,
                    ]);
                }
            });
        } catch (Throwable $e) {
            // Nothing was saved, so remove the files that were already copied.
            foreach ($stored as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        $count = count($stored);

        return $this->saved($count === 1 ? 'Photo added.' : "{$count} photos added.");
    }

    /** Change the alt text, switch a photo on or off, or replace its file. */
    public function update(Request $request, AboutPhoto $photo): RedirectResponse
    {
        $limitMb = self::MAX_KB / 1024;

        $data = $request->validateWithBag('gallery_'.$photo->id, [
            'alt' => ['nullable', 'string', 'max:160'],
            'is_active' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:'.self::MAX_KB],
        ], [
            'alt.max' => 'The alt text must not be longer than 160 characters.',
            'photo.image' => 'The file must be an image.',
            'photo.mimes' => 'The photo must be a PNG, JPG, or WebP image.',
            'photo.max' => "The photo must not be larger than {$limitMb} MB.",
            'photo.uploaded' => 'Upload failed. The file may exceed the server limit (upload_max_filesize in php.ini).',
        ]);

        $old = null;

        if ($request->hasFile('photo')) {
            // Save the new file first. The old one is deleted only after the row is saved.
            $old = $photo->path;
            $photo->path = AdminForm::storeOptimized($request->file('photo'), 'about', 1600);
        }

        $photo->alt = $data['alt'] ?? null;
        $photo->is_active = (bool) ($data['is_active'] ?? false);
        $photo->save();

        $this->deleteFileIfUnused($old);

        return $this->saved('Photo saved.');
    }

    /** Delete a photo and its file. */
    public function destroy(AboutPhoto $photo): RedirectResponse
    {
        $path = $photo->path;

        DB::transaction(function () use ($photo) {
            $photo->delete();
            $this->renumber();
        });

        $this->deleteFileIfUnused($path);

        return $this->saved('Photo deleted.');
    }

    /** Save the order after drag and drop. `ids` is the list of photo ids from first to last. */
    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'max:500'],
            'ids.*' => ['integer', 'distinct'],
        ]);

        DB::transaction(function () use ($data) {
            $existing = AboutPhoto::query()->ordered()->pluck('id')->all();
            $given = array_values(array_intersect(array_map('intval', $data['ids']), $existing));
            // A photo that was not in the list (for example added in another tab) keeps its place at the end.
            $rest = array_values(array_diff($existing, $given));

            foreach (array_merge($given, $rest) as $position => $id) {
                AboutPhoto::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return response()->json(['ok' => true]);
    }

    /** Number the photos 1, 2, 3 ... again so there are no gaps after a delete. */
    private function renumber(): void
    {
        AboutPhoto::query()->ordered()->pluck('id')->each(
            fn ($id, $position) => AboutPhoto::whereKey($id)->update(['sort_order' => $position + 1])
        );
    }

    /** Delete a file only when no gallery photo still points to it. */
    private function deleteFileIfUnused(?string $path): void
    {
        if ($path && ! AboutPhoto::where('path', $path)->exists()) {
            Storage::disk('public')->delete($path);
        }
    }

    private function saved(string $message): RedirectResponse
    {
        return redirect()
            ->to(route('admin.site.edit').'#sec-gallery')
            ->with('status', $message)
            ->with('section', 'gallery');
    }
}
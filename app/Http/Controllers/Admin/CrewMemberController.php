<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrewMember;
use App\Models\SiteSetting;
use App\Models\TeamDivision;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CrewMemberController extends Controller
{
    public function index()
    {
        return view('admin.team.index', [
            'items'     => CrewMember::with('division')->ordered()->get(),
            'divisions' => TeamDivision::ordered()->withCount('members')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.team.create', ['item' => new CrewMember, 'divisions' => TeamDivision::ordered()->get()]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new CrewMember);

        return redirect()->route('admin.team.index')->with('status', 'Team member added.');
    }

    public function edit(CrewMember $item)
    {
        return view('admin.team.edit', ['item' => $item, 'divisions' => TeamDivision::ordered()->get()]);
    }

    public function update(Request $request, CrewMember $item)
    {
        $this->persist($request, $item);

        return redirect()->route('admin.team.index')->with('status', 'Team member updated.');
    }

    public function destroy(CrewMember $item)
    {
        if ($item->photo) {
            Storage::disk('public')->delete($item->photo);
        }
        $item->delete();

        return redirect()->route('admin.team.index')->with('status', 'Team member deleted.');
    }

    public function move(Request $request, CrewMember $item)
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);
        Sortable::move(CrewMember::class, $item, $request->input('direction'));

        return back();
    }

    /** Add a division. */
    public function storeDivision(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('division', ['name' => ['required', 'string', 'max:120']]);

        TeamDivision::create(['name' => $data['name'], 'sort_order' => (TeamDivision::max('sort_order') ?? 0) + 1]);

        return redirect()->route('admin.team.index')->with('status', 'Division added.');
    }

    /** Rename a division. */
    public function updateDivision(Request $request, TeamDivision $division): RedirectResponse
    {
        $data = $request->validateWithBag('division', ['name' => ['required', 'string', 'max:120']]);

        $division->update(['name' => $data['name']]);

        return redirect()->route('admin.team.index')->with('status', 'Division renamed.');
    }

    /** Delete a division. The people in it stay, and have no division. */
    public function destroyDivision(TeamDivision $division): RedirectResponse
    {
        CrewMember::where('team_division_id', $division->id)->update(['team_division_id' => null]);
        $division->delete();

        return redirect()->route('admin.team.index')->with('status', 'Division deleted. Its people are now without a division.');
    }

    /** Save the order of the divisions after a drag and drop. */
    public function reorderDivisions(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => ['required', 'array', 'max:200'],
            'ids.*' => ['integer'],
        ]);

        $known = TeamDivision::whereIn('id', $data['ids'])->pluck('id')->all();

        DB::transaction(function () use ($data, $known) {
            $position = 0;

            foreach ($data['ids'] as $id) {
                if (in_array((int) $id, $known, true)) {
                    TeamDivision::whereKey($id)->update(['sort_order' => ++$position]);
                }
            }
        });

        return response()->json(['ok' => true]);
    }

    /** Save the order after a drag and drop in the list. ids = every person, in the new order. */
    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => ['required', 'array', 'max:500'],
            'ids.*' => ['integer'],
        ]);

        $known = CrewMember::whereIn('id', $data['ids'])->pluck('id')->all();

        DB::transaction(function () use ($data, $known) {
            $position = 0;

            foreach ($data['ids'] as $id) {
                if (in_array((int) $id, $known, true)) {
                    CrewMember::whereKey($id)->update(['sort_order' => ++$position]);
                }
            }
        });

        return response()->json(['ok' => true]);
    }

    /** Save the hero photo of the public Team page: the file (optional when one is already saved), its position, and its zoom. */
    public function updateHero(Request $request): RedirectResponse
    {
        $site = SiteSetting::current();

        $request->validate([
            'team_image' => [Rule::requiredIf(blank($site->team_image)), 'nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'focus_x'    => ['nullable', 'numeric', 'between:0,100'],
            'focus_y'    => ['nullable', 'numeric', 'between:0,100'],
            'zoom'       => ['nullable', 'numeric', 'between:1,3'],
        ], [
            'team_image.required' => 'Choose a photo first.',
            'team_image.image'    => 'The file must be an image.',
            'team_image.mimes'    => 'The photo must be a PNG, JPG, or WebP file.',
            'team_image.max'      => 'The photo may not be larger than 5 MB.',
            'team_image.uploaded' => 'Upload failed. The file may exceed the server limit (upload_max_filesize in php.ini).',
        ]);

        $old = null;

        if ($request->hasFile('team_image')) {
            $old = $site->team_image;
            $site->team_image = AdminForm::storeOptimized($request->file('team_image'), 'site', 2000);
        }

        $site->team_image_x    = (float) $request->input('focus_x', 50);
        $site->team_image_y    = (float) $request->input('focus_y', 50);
        $site->team_image_zoom = (float) $request->input('zoom', 1);
        $site->save();

        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return redirect()->route('admin.team.index')->with('status', 'Hero photo saved.');
    }

    /** Remove the hero photo of the public Team page. */
    public function destroyHero(): RedirectResponse
    {
        $site = SiteSetting::current();
        $old  = $site->team_image;

        $site->team_image = null;
        $site->save();

        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return redirect()->route('admin.team.index')->with('status', 'Hero photo removed.');
    }

    private function persist(Request $request, CrewMember $item): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'string', 'max:120'],
            'credits' => ['nullable', 'string', 'max:2000'],
            'photo' => AdminForm::imageRules(),
            'team_division_id' => ['nullable', 'integer', 'exists:team_divisions,id'],
            'photo_x'    => ['nullable', 'numeric', 'between:0,100'],
            'photo_y'    => ['nullable', 'numeric', 'between:0,100'],
            'photo_zoom' => ['nullable', 'numeric', 'between:1,3'],
        ]);

        // Only 3 people can be shown on the Home and About pages.
        $featured = $request->boolean('is_featured');

        if ($featured) {
            $others = CrewMember::where('is_featured', true)
                ->when($item->exists, fn ($query) => $query->whereKeyNot($item->id))
                ->count();

            if ($others >= 3) {
                throw ValidationException::withMessages([
                    'is_featured' => 'Only 3 people can be shown on the Home and About pages. Untick one of them first.',
                ]);
            }
        }

        $item->name = $data['name'];
        $item->role = $data['role'];
        $item->credits = $data['credits'] ?? null;
        $item->photo = AdminForm::syncImage($request, $item->photo, 'photo', 'team');
        $item->team_division_id = $data['team_division_id'] ?? null;
        $item->photo_x    = (float) ($data['photo_x'] ?? 50);
        $item->photo_y    = (float) ($data['photo_y'] ?? 100);
        $item->photo_zoom = (float) ($data['photo_zoom'] ?? 1);
        $item->is_featured = $featured;
        $item->is_active = $request->boolean('is_active');

        if (! $item->exists) {
            $item->sort_order = (CrewMember::max('sort_order') ?? 0) + 1;
        }

        $item->save();
    }
}
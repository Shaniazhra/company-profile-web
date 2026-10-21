<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrewMember;
use App\Models\SiteSetting;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CrewMemberController extends Controller
{
    public function index()
    {
        return view('admin.team.index', ['items' => CrewMember::ordered()->get()]);
    }

    public function create()
    {
        return view('admin.team.create', ['item' => new CrewMember]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new CrewMember);

        return redirect()->route('admin.team.index')->with('status', 'Team member added.');
    }

    public function edit(CrewMember $item)
    {
        return view('admin.team.edit', ['item' => $item]);
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
            $site->team_image = $request->file('team_image')->store('site', 'public');
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
        ]);

        $item->name = $data['name'];
        $item->role = $data['role'];
        $item->credits = $data['credits'] ?? null;
        $item->photo = AdminForm::syncImage($request, $item->photo, 'photo', 'team');
        $item->is_active = $request->boolean('is_active');

        if (! $item->exists) {
            $item->sort_order = (CrewMember::max('sort_order') ?? 0) + 1;
        }

        $item->save();
    }
}
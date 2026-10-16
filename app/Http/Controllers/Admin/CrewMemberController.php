<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrewMember;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
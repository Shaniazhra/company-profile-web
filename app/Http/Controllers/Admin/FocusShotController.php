<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FocusShot;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FocusShotController extends Controller
{
    public function index()
    {
        return view('admin.focus.index', ['items' => FocusShot::ordered()->get()]);
    }

    public function create()
    {
        return view('admin.focus.create', ['item' => new FocusShot]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new FocusShot);

        return redirect()->route('admin.focus.index')->with('status', 'Screenshot added.');
    }

    public function edit(FocusShot $item)
    {
        return view('admin.focus.edit', ['item' => $item]);
    }

    public function update(Request $request, FocusShot $item)
    {
        $this->persist($request, $item);

        return redirect()->route('admin.focus.index')->with('status', 'Screenshot updated.');
    }

    public function destroy(FocusShot $item)
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $item->delete();

        return redirect()->route('admin.focus.index')->with('status', 'Screenshot deleted.');
    }

    public function move(Request $request, FocusShot $item)
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);
        Sortable::move(FocusShot::class, $item, $request->input('direction'));

        return back();
    }

    private function persist(Request $request, FocusShot $item): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'image' => AdminForm::imageRules(),
        ]);

        $item->title = $data['title'];
        $item->is_active = $request->boolean('is_active');
        $item->image = AdminForm::syncImage($request, $item->image, 'image', 'focus');

        if (! $item->exists) {
            $item->sort_order = (FocusShot::max('sort_order') ?? 0) + 1;
        }

        $item->save();
    }
}
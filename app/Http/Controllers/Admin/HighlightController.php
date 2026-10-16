<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Highlight;
use App\Support\Sortable;
use Illuminate\Http\Request;

class HighlightController extends Controller
{
    public function index()
    {
        return view('admin.highlights.index', [
            'groups' => Highlight::ordered()->get()->groupBy('group'),
        ]);
    }

    public function create()
    {
        return view('admin.highlights.create', ['item' => new Highlight]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new Highlight);

        return redirect()->route('admin.highlights.index')->with('status', 'Highlight added.');
    }

    public function edit(Highlight $item)
    {
        return view('admin.highlights.edit', ['item' => $item]);
    }

    public function update(Request $request, Highlight $item)
    {
        $this->persist($request, $item);

        return redirect()->route('admin.highlights.index')->with('status', 'Highlight updated.');
    }

    public function destroy(Highlight $item)
    {
        $item->delete();

        return redirect()->route('admin.highlights.index')->with('status', 'Highlight deleted.');
    }

    // Reorder only among items in the same group.
    public function move(Request $request, Highlight $item)
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);
        Sortable::move(Highlight::class, $item, $request->input('direction'), fn ($q) => $q->where('group', $item->group));

        return back();
    }

    private function persist(Request $request, Highlight $item): void
    {
        $data = $request->validate([
            'group' => ['required', 'in:reason,result'],
            'title' => ['nullable', 'string', 'max:120', 'required_if:group,reason'],
            'text' => ['required', 'string', 'max:400'],
            'icon' => ['nullable', 'in:'.implode(',', array_keys(Highlight::ICONS))],
        ]);

        $item->group = $data['group'];
        $item->title = $data['title'] ?? null;
        $item->text = $data['text'];
        $item->icon = $data['icon'] ?? 'star';
        $item->is_active = $request->boolean('is_active');

        if (! $item->exists || $item->isDirty('group')) {
            $item->sort_order = (Highlight::where('group', $data['group'])->max('sort_order') ?? 0) + 1;
        }

        $item->save();
    }
}
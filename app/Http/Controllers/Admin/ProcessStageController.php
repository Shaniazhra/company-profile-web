<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcessStage;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcessStageController extends Controller
{
    public function index()
    {
        return view('admin.process.index', ['items' => ProcessStage::ordered()->get()]);
    }

    public function create()
    {
        return view('admin.process.create', ['item' => new ProcessStage]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new ProcessStage);

        return redirect()->route('admin.process.index')->with('status', 'Stage added.');
    }

    public function edit(ProcessStage $item)
    {
        return view('admin.process.edit', ['item' => $item]);
    }

    public function update(Request $request, ProcessStage $item)
    {
        $this->persist($request, $item);

        return redirect()->route('admin.process.index')->with('status', 'Stage updated.');
    }

    public function destroy(ProcessStage $item)
    {

        $item->delete();

        return redirect()->route('admin.process.index')->with('status', 'Stage deleted.');
    }

    public function move(Request $request, ProcessStage $item)
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);
        Sortable::move(ProcessStage::class, $item, $request->input('direction'));

        return back();
    }

    private function persist(Request $request, ProcessStage $item): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'summary' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:5000'],
        ]);

        $item->title = $data['title'];
        $item->summary = $data['summary'] ?? null;
        $item->details = $data['details'] ?? null;
        $item->is_active = $request->boolean('is_active');

        if (! $item->exists) {
            $item->sort_order = (ProcessStage::max('sort_order') ?? 0) + 1;
        }

        $item->save();
    }
}
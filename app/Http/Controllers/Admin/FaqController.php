<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faqs.index', ['items' => Faq::ordered()->get()]);
    }

    public function create()
    {
        return view('admin.faqs.create', ['item' => new Faq]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new Faq);

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ added.');
    }

    public function edit(Faq $item)
    {
        return view('admin.faqs.edit', ['item' => $item]);
    }

    public function update(Request $request, Faq $item)
    {
        $this->persist($request, $item);

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ updated.');
    }

    public function destroy(Faq $item)
    {

        $item->delete();

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ deleted.');
    }

    public function move(Request $request, Faq $item)
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);
        Sortable::move(Faq::class, $item, $request->input('direction'));

        return back();
    }

    // Drag and drop on the FAQ list: saves the new order in one go.
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', Rule::exists((new Faq)->getTable(), 'id')],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['ids'] as $position => $id) {
                Faq::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return response()->json(['ok' => true]);
    }

    private function persist(Request $request, Faq $item): void
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:3000'],
        ]);

        $item->question = $data['question'];
        $item->answer = $data['answer'];
        $item->is_active = $request->boolean('is_active');

        if (! $item->exists) {
            $item->sort_order = (Faq::max('sort_order') ?? 0) + 1;
        }

        $item->save();
    }
}
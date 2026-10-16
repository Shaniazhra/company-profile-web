<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactPerson;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactPersonController extends Controller
{
    public function index()
    {
        return view('admin.contacts.index', ['items' => ContactPerson::ordered()->get()]);
    }

    public function create()
    {
        return view('admin.contacts.create', ['item' => new ContactPerson]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new ContactPerson);

        return redirect()->route('admin.contacts.index')->with('status', 'Contact added.');
    }

    public function edit(ContactPerson $item)
    {
        return view('admin.contacts.edit', ['item' => $item]);
    }

    public function update(Request $request, ContactPerson $item)
    {
        $this->persist($request, $item);

        return redirect()->route('admin.contacts.index')->with('status', 'Contact updated.');
    }

    public function destroy(ContactPerson $item)
    {

        $item->delete();

        return redirect()->route('admin.contacts.index')->with('status', 'Contact deleted.');
    }

    public function move(Request $request, ContactPerson $item)
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);
        Sortable::move(ContactPerson::class, $item, $request->input('direction'));

        return back();
    }

    private function persist(Request $request, ContactPerson $item): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:190'],
        ]);

        $item->name = $data['name'];
        $item->role = $data['role'] ?? null;
        $item->phone = $data['phone'] ?? null;
        $item->email = $data['email'] ?? null;
        $item->is_active = $request->boolean('is_active');

        if (! $item->exists) {
            $item->sort_order = (ContactPerson::max('sort_order') ?? 0) + 1;
        }

        $item->save();
    }
}
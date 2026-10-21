<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index()
    {
        return view('admin.clients.index', [
            'clients' => Client::ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.clients.create', ['client' => new Client]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new Client);

        return redirect()->route('admin.clients.index')->with('status', 'Client added.');
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', ['client' => $client]);
    }

    public function update(Request $request, Client $client)
    {
        $this->persist($request, $client);

        return redirect()->route('admin.clients.index')->with('status', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        if ($client->logo) {
            Storage::disk('public')->delete($client->logo);
        }
        $client->delete();

        return redirect()->route('admin.clients.index')->with('status', 'Client deleted.');
    }

    public function move(Request $request, Client $client)
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);
        Sortable::move(Client::class, $client, $request->input('direction'));

        return back();
    }

    // Drag and drop on the Clients list: saves the new order in one go.
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:clients,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['ids'] as $position => $id) {
                Client::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return response()->json(['ok' => true]);
    }

    private function persist(Request $request, Client $client): void
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'website_url' => ['nullable', 'url:http,https', 'max:255'],
            'logo' => AdminForm::imageRules(),
            'is_active' => ['nullable', 'boolean'],
        ]);

        $client->name = $data['name'];
        $client->website_url = $data['website_url'] ?? null;
        $client->is_active = $request->boolean('is_active');
        $client->logo = AdminForm::syncImage($request, $client->logo, 'logo', 'clients');

        if (! $client->exists) {
            $client->sort_order = (Client::max('sort_order') ?? 0) + 1;
        }

        $client->save();
    }
}
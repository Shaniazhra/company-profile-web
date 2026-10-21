<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\AdminForm;
use App\Support\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', [
            'services' => Service::ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.services.create', ['service' => new Service]);
    }

    public function store(Request $request)
    {
        $this->persist($request, new Service);

        return redirect()->route('admin.services.index')->with('status', 'Service added.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', ['service' => $service]);
    }

    public function update(Request $request, Service $service)
    {
        $this->persist($request, $service);

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    public function move(Request $request, Service $service)
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);
        Sortable::move(Service::class, $service, $request->input('direction'));

        return back();
    }

    // Drag and drop on the Services list: saves the new order in one go.
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:services,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['ids'] as $position => $id) {
                Service::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return response()->json(['ok' => true]);
    }

    private function persist(Request $request, Service $service): void
    {
        $data = $request->validate([
            'image' => AdminForm::imageRules(),
            'is_active' => ['nullable', 'boolean'],
            ...AdminForm::translationRules('title', required: true, max: 150),
            ...AdminForm::translationRules('summary', max: 300),
        ]);

        foreach (['title', 'summary'] as $field) {
            $service->setTranslations($field, AdminForm::clean($data[$field] ?? []));
        }

        // Description and icon are no longer edited in the admin panel.
        // New services get empty values so the database columns are always filled.
        if (! $service->exists) {
            $service->setTranslations('description', []);
            $service->icon = null;
        }

        $service->is_active = $request->boolean('is_active');
        $service->image = AdminForm::syncImage($request, $service->image, 'image', 'services');

        if (! $service->exists) {
            $service->sort_order = (Service::max('sort_order') ?? 0) + 1;
        }

        $service->save();
    }
}
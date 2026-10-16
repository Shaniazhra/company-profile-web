<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\AdminForm;
use App\Support\Icons;
use App\Support\Sortable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

    private function persist(Request $request, Service $service): void
    {
        $data = $request->validate([
            'icon' => ['nullable', Rule::in(array_keys(Icons::all()))],
            'image' => AdminForm::imageRules(),
            'is_active' => ['nullable', 'boolean'],
            ...AdminForm::translationRules('title', required: true, max: 150),
            ...AdminForm::translationRules('summary', max: 300),
            ...AdminForm::translationRules('description', max: 3000),
        ]);

        foreach (['title', 'summary', 'description'] as $field) {
            $service->setTranslations($field, AdminForm::clean($data[$field] ?? []));
        }

        $service->icon = $data['icon'] ?? null;
        $service->is_active = $request->boolean('is_active');
        $service->image = AdminForm::syncImage($request, $service->image, 'image', 'services');

        if (! $service->exists) {
            $service->sort_order = (Service::max('sort_order') ?? 0) + 1;
        }

        $service->save();
    }
}
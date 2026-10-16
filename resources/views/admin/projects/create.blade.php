<x-admin-layout title="Tambah Proyek">
    @include('admin.projects._form', [
        'project' => $project,
        'action'  => route('admin.projects.store'),
        'method'  => 'POST',
    ])
</x-admin-layout>
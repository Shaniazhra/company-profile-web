<x-admin-layout title="Add Project">
    @include('admin.projects._form', [
        'project' => $project,
        'action'  => route('admin.projects.store'),
        'method'  => 'POST',
    ])
</x-admin-layout>
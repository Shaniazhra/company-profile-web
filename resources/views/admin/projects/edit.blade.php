<x-admin-layout title="Edit Project">
    @include('admin.projects._form', [
        'project' => $project,
        'action'  => route('admin.projects.update', $project),
        'method'  => 'PUT',
    ])
</x-admin-layout>
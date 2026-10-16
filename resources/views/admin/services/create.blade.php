<x-admin-layout>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <h1 class="mb-4 px-4 sm:px-0 text-xl font-semibold text-gray-800">Add Service</h1>

            <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data"
                  class="space-y-8 bg-white p-6 shadow sm:rounded-lg">
                @csrf
                @include('admin.services._form')
            </form>
        </div>
    </div>
</x-admin-layout>
<x-admin-layout>

    <div class="w-full py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="mb-4 text-xl font-semibold text-gray-800">Edit FAQ</h1>

        <form method="POST" action="{{ route('admin.faqs.update', $item) }}" enctype="multipart/form-data"
              class="w-full space-y-8 bg-white p-6 shadow sm:rounded-lg">
            @csrf
            @method('PUT')
            @include('admin.faqs._form')
        </form>
    </div>
</x-admin-layout>
@props([
    'name',
    'kind' => 'logo',
    'title',
    'description' => null,
    'path' => null,
    'uploadRoute',
    'deleteRoute',
    'help' => null,
    'maxMb' => 2,
    'dark' => false,
    'message' => null,
])

<section id="sec-{{ $name }}" class="site-card is-{{ $kind }}"
         x-data="{
             max: {{ (int) $maxMb }},
             preview: null,
             fileName: '',
             error: '',
             pick(event) {
                 const file = event.target.files[0];
                 this.error = '';
                 if (this.preview) URL.revokeObjectURL(this.preview);
                 this.preview = null;
                 if (!file) return;
                 if (file.size > this.max * 1024 * 1024) {
                     this.error = 'Ukuran file ' + (file.size / 1048576).toFixed(1) + ' MB melebihi batas ' + this.max + ' MB.';
                     event.target.value = '';
                     return;
                 }
                 this.preview = URL.createObjectURL(file);
                 this.fileName = file.name;
             },
             cancel() {
                 if (this.preview) URL.revokeObjectURL(this.preview);
                 this.preview = null;
                 this.fileName = '';
                 this.error = '';
                 this.$refs.file.value = '';
             }
         }">

    <div class="site-card-head">
        <h3>{{ $title }}</h3>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>

    <div class="site-card-body">

        {{-- Pratinjau: gambar sekarang, atau gambar baru yang baru dipilih --}}
        <div class="img-up-stage {{ $dark ? 'is-dark' : '' }}">
            <template x-if="preview">
                <img :src="preview" alt="Pratinjau {{ $title }}" class="img-up-img">
            </template>

            @if ($path)
                <img x-show="!preview" src="{{ asset('storage/'.$path) }}" alt="{{ $title }}" class="img-up-img">
            @else
                <p x-show="!preview" class="img-up-empty">Belum ada gambar</p>
            @endif

            <span x-show="preview" x-cloak class="img-up-badge">Pratinjau, belum diunggah</span>
        </div>

        {{-- Form unggah: tombol Unggah dan Batal baru muncul setelah file dipilih --}}
        <form id="upload-{{ $name }}" method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="img-up-controls">
            @csrf
            <input x-ref="file" id="{{ $name }}" type="file" name="{{ $name }}"
                   accept="image/png,image/jpeg,image/webp" class="img-up-file" @change="pick($event)">

            <div x-show="preview" x-cloak class="img-up-actions">
                <span class="img-up-filename" x-text="fileName"></span>
                <button type="submit" class="btn btn-primary btn-sm">Unggah</button>
                <button type="button" class="btn btn-outline btn-sm" @click="cancel()">Batal</button>
            </div>
        </form>

        <p class="img-up-error" x-show="error" x-text="error" x-cloak></p>
        <x-input-error :messages="$errors->get($name)" class="mt-2" />

        @if ($help)
            <p class="img-up-hint">{{ $help }}</p>
        @endif
    </div>

    {{-- Hapus: hanya ada kalau gambar sudah ada. Form terpisah supaya tidak bersarang. --}}
    <div class="site-card-foot">
        @if ($message)
            <p class="site-note" role="status">
                <x-admin.icon name="check" class="h-4 w-4" />
                {{ $message }}
            </p>
        @endif

        @if ($path)
            <form id="delete-{{ $name }}" method="POST" action="{{ $deleteRoute }}"
                  onsubmit="return confirm('Hapus gambar ini dari website?')">
                @csrf
                @method('DELETE')
            </form>
            <button type="submit" form="delete-{{ $name }}" class="btn btn-danger btn-sm" x-show="!preview">Hapus</button>
        @endif
    </div>
</section>
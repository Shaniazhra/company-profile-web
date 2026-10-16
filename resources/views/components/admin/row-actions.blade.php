@props(['edit', 'delete', 'move' => null, 'first' => false, 'last' => false, 'confirm' => 'Delete this item? This action cannot be undone.'])

<div class="flex items-center gap-2">
    @if ($move)
        <form method="POST" action="{{ $move }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="direction" value="up">
            <button type="submit" class="btn btn-outline btn-sm btn-icon" @disabled($first) aria-label="Naikkan">↑</button>
        </form>
        <form method="POST" action="{{ $move }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="direction" value="down">
            <button type="submit" class="btn btn-outline btn-sm btn-icon" @disabled($last) aria-label="Turunkan">↓</button>
        </form>
    @endif

    <a href="{{ $edit }}" class="btn btn-outline btn-sm">Ubah</a>

    <form method="POST" action="{{ $delete }}" onsubmit="return confirm(@js($confirm))">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
    </form>
</div>
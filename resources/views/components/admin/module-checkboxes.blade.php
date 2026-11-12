@props(['model', 'idPrefix' => 'akses'])

{{-- Kotak centang akses menu panel admin. $model = nama properti Livewire berisi array kunci modul. --}}
<fieldset>
    <legend class="block text-sm font-medium text-ink-soft">
        Akses menu <span class="text-danger-text">*</span>
    </legend>
    <p class="mt-0.5 text-xs text-ink-soft">Centang menu yang boleh dibuka dan dikelola akun ini.</p>

    <div class="mt-2 grid gap-2 sm:grid-cols-2">
        @foreach (\App\Models\User::MODULES as $key => $label)
            <label for="{{ $idPrefix }}-{{ $key }}"
                   class="flex cursor-pointer items-center gap-3 rounded-lg border border-line bg-bone px-3 py-2.5 text-sm transition hover:bg-paper">
                <input id="{{ $idPrefix }}-{{ $key }}" type="checkbox" value="{{ $key }}"
                       wire:model="{{ $model }}"
                       class="h-4 w-4 rounded border-line text-brand focus:ring-teal-bright">
                <span class="font-medium text-ink">{{ $label }}</span>
            </label>
        @endforeach
    </div>

    @error($model)
        <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
    @enderror
</fieldset>

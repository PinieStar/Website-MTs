@php
    $toolbarButton = 'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-ink transition hover:bg-line focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-bright';
    $toolbarActive = 'bg-brand text-on-brand hover:bg-brand-hover';
    $toolbarDivider = 'mx-1 hidden h-5 w-px shrink-0 bg-line sm:block';
    $toolbarSelect = 'h-8 max-w-[8.5rem] shrink-0 rounded-md border border-line bg-bone px-1.5 text-xs font-medium text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright';
@endphp

{{-- Toolbar editor: dibungkus flex-wrap agar tetap bisa dipakai di layar HP --}}
<div class="flex shrink-0 flex-wrap items-center gap-1 border-b border-line bg-paper p-2">
    <button type="button" @click="undo()" title="Undo" aria-label="Undo"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9 7 4 12l5 5" />
            <path d="M4 12h9a7 7 0 0 1 7 7" />
        </svg>
    </button>
    <button type="button" @click="redo()" title="Redo" aria-label="Redo"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m15 7 5 5-5 5" />
            <path d="M20 12h-9a7 7 0 0 0-7 7" />
        </svg>
    </button>

    <span class="{{ $toolbarDivider }}"></span>

    <select @change="setBlockType($event.target.value)" :value="active.block"
            title="Format blok" aria-label="Format blok"
            class="{{ $toolbarSelect }}">
        <option value="p">Paragraf</option>
        <option value="h1">Judul 1</option>
        <option value="h2">Judul 2</option>
        <option value="h3">Judul 3</option>
        <option value="h4">Judul 4</option>
    </select>

    <select @change="setFontSize($event.target.value)" :value="active.fontSize"
            title="Ukuran teks" aria-label="Ukuran teks"
            class="{{ $toolbarSelect }}">
        <option value="">Ukuran normal</option>
        <option value="0.875em">Kecil</option>
        <option value="1.125em">Sedang</option>
        <option value="1.25em">Besar</option>
        <option value="1.5em">Sangat besar</option>
    </select>

    <label class="relative inline-flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-md text-ink transition hover:bg-line"
           title="Warna teks" aria-label="Warna teks">
        <span class="pointer-events-none text-sm font-bold leading-none" :style="`color: ${active.color || 'currentColor'}`">A</span>
        <span class="pointer-events-none absolute bottom-1 left-1/2 h-1 w-4 -translate-x-1/2 rounded-full"
              :style="`background-color: ${active.color || '#0f172a'}`"></span>
        <input type="color" :value="active.color || '#0f172a'"
               @input="setColor($event.target.value)"
               class="absolute inset-0 cursor-pointer opacity-0" aria-label="Pilih warna teks">
    </label>

    <span class="{{ $toolbarDivider }}"></span>

    <button type="button" @click="toggleBold()" title="Tebal" aria-label="Tebal"
            :aria-pressed="active.bold.toString()"
            :class="active.bold ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }} text-sm font-bold">
        B
    </button>
    <button type="button" @click="toggleItalic()" title="Miring" aria-label="Miring"
            :aria-pressed="active.italic.toString()"
            :class="active.italic ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }} font-serif text-sm italic">
        I
    </button>
    <button type="button" @click="toggleUnderline()" title="Garis bawah" aria-label="Garis bawah"
            :aria-pressed="active.underline.toString()"
            :class="active.underline ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }} text-sm font-semibold underline underline-offset-2">
        U
    </button>
    <button type="button" @click="toggleStrike()" title="Coret" aria-label="Coret"
            :aria-pressed="active.strike.toString()"
            :class="active.strike ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }} text-sm font-semibold line-through">
        S
    </button>
    <button type="button" @click="toggleCode()" title="Kode sebaris" aria-label="Kode sebaris"
            :aria-pressed="active.code.toString()"
            :class="active.code ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m9 8-4 4 4 4" />
            <path d="m15 8 4 4-4 4" />
        </svg>
    </button>
    <button type="button" @click="toggleHighlight()" title="Stabilo" aria-label="Stabilo"
            :aria-pressed="active.highlight.toString()"
            :class="active.highlight ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m9 11 4 4" />
            <path d="M5 19h14" />
            <path d="M15 5 9 11l-2 5 5-2 6-6a2.1 2.1 0 0 0-3-3Z" />
        </svg>
    </button>

    <span class="{{ $toolbarDivider }}"></span>

    <button type="button" @click="setTextAlign('left')" title="Rata kiri" aria-label="Rata kiri"
            :aria-pressed="(active.alignment === 'left').toString()"
            :class="active.alignment === 'left' ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
            <path d="M4 6h16M4 12h10M4 18h13" />
        </svg>
    </button>
    <button type="button" @click="setTextAlign('center')" title="Rata tengah" aria-label="Rata tengah"
            :aria-pressed="(active.alignment === 'center').toString()"
            :class="active.alignment === 'center' ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
            <path d="M4 6h16M7 12h10M5 18h14" />
        </svg>
    </button>
    <button type="button" @click="setTextAlign('right')" title="Rata kanan" aria-label="Rata kanan"
            :aria-pressed="(active.alignment === 'right').toString()"
            :class="active.alignment === 'right' ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
            <path d="M4 6h16M10 12h10M7 18h13" />
        </svg>
    </button>
    <button type="button" @click="setTextAlign('justify')" title="Rata kiri-kanan" aria-label="Rata kiri-kanan"
            :aria-pressed="(active.alignment === 'justify').toString()"
            :class="active.alignment === 'justify' ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
            <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <span class="{{ $toolbarDivider }}"></span>

    <button type="button" @click="toggleBulletList()" title="Daftar poin" aria-label="Daftar poin"
            :aria-pressed="active.bulletList.toString()"
            :class="active.bulletList ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
            <path d="M9 6h11M9 12h11M9 18h11" />
            <circle cx="4.5" cy="6" r="1" fill="currentColor" stroke="none" />
            <circle cx="4.5" cy="12" r="1" fill="currentColor" stroke="none" />
            <circle cx="4.5" cy="18" r="1" fill="currentColor" stroke="none" />
        </svg>
    </button>
    <button type="button" @click="toggleOrderedList()" title="Daftar bernomor" aria-label="Daftar bernomor"
            :aria-pressed="active.orderedList.toString()"
            :class="active.orderedList ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
            <path d="M9 6h11M9 12h11M9 18h11" />
            <path d="M4 5h1v3M3.5 8h2M3.5 12h2l-2 3h2" />
        </svg>
    </button>
    <button type="button" @click="toggleBlockquote()" title="Kutipan" aria-label="Kutipan"
            :aria-pressed="active.blockquote.toString()"
            :class="active.blockquote ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M10 7H6v5h4v5H6" />
            <path d="M20 7h-4v5h4v5h-4" />
        </svg>
    </button>
    <button type="button" @click="toggleCodeBlock()" title="Blok kode" aria-label="Blok kode"
            :aria-pressed="active.codeBlock.toString()"
            :class="active.codeBlock ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m8 8-4 4 4 4" />
            <path d="m16 8 4 4-4 4" />
            <path d="M14 4 10 20" />
        </svg>
    </button>
    <button type="button" @click="insertHorizontalRule()" title="Garis pemisah" aria-label="Garis pemisah"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
            <path d="M4 12h16" />
            <path d="M4 6h4M16 6h4M4 18h4M16 18h4" />
        </svg>
    </button>

    <span class="{{ $toolbarDivider }}"></span>

    <button type="button" @click="setLink()" title="Sisipkan tautan" aria-label="Sisipkan tautan"
            :aria-pressed="active.link.toString()"
            :class="active.link ? '{{ $toolbarActive }}' : ''"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M10 13a5 5 0 0 0 7.5.5l2-2a5 5 0 0 0-7-7l-1.5 1.5" />
            <path d="M14 11a5 5 0 0 0-7.5-.5l-2 2a5 5 0 0 0 7 7l1.5-1.5" />
        </svg>
    </button>
    <button type="button" @click="insertImage()" :disabled="uploading"
            title="Sisipkan gambar" aria-label="Sisipkan gambar"
            class="{{ $toolbarButton }} disabled:cursor-wait disabled:opacity-50">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="4" width="18" height="16" rx="2" />
            <circle cx="8.5" cy="9.5" r="1.5" />
            <path d="m21 15-4.5-4.5L7 20" />
        </svg>
    </button>
    <button type="button" @click="clearFormatting()" title="Hapus format" aria-label="Hapus format"
            class="{{ $toolbarButton }}">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m7 21-4-4 9-9 4 4-9 9Z" />
            <path d="M14 4l4 4" />
            <path d="M3 21h8" />
        </svg>
    </button>
</div>

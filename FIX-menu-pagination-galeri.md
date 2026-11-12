# Perbaikan: Menu Admin, Pagination, dan Galeri Publik

Tiga perbaikan terpisah. Kerjakan satu per satu, boleh satu prompt per
bagian kalau mau direview terpisah.

---

## 1. Pisahkan menu "Berita" dan "Artikel" di admin panel

Saat ini sidebar admin cuma punya 1 menu "Berita & Artikel" →
`/admin/posts` (route `admin.posts.index`), dengan filter `type` di
dalam komponen `PostManager`. Pisahkan jadi 2 menu & 2 halaman:

1. Ubah routing di `routes/web.php`: ganti route tunggal
   `admin.posts.index` jadi dua route — `admin.posts.berita` →
   `/admin/posts/berita` dan `admin.posts.artikel` → `/admin/posts/artikel`.
2. `PostManager` (Livewire component) tetap SATU class (jangan duplikat
   jadi 2 component terpisah — logic CRUD-nya identik, cuma beda default
   filter). Cara paling sederhana: terima parameter `type` dari route/mount
   (`mount(string $type)`), set `$this->filterType` dan `$this->type`
   sesuai parameter itu, dan sembunyikan toggle pilihan "Tipe" di form
   create/edit karena sudah ditentukan dari halaman mana admin masuk
   (kalau admin buka halaman Berita, form create otomatis `type=berita`
   tanpa perlu memilih lagi).
3. Update `resources/views/layouts/admin.blade.php`: ganti 1 entri menu
   `'Berita & Artikel'` jadi 2 entri terpisah: `'Berita'` →
   `admin.posts.berita`, `'Artikel'` → `admin.posts.artikel`. Pertahankan
   ikon yang relevan untuk masing-masing (boleh beda ikon antara Berita
   dan Artikel supaya gampang dibedakan sekilas).
4. Update test yang ada di `tests/Feature/.../PostManagerTest.php` (atau
   sejenisnya) supaya mengetes kedua route baru, termasuk: form create di
   halaman Berita tidak menampilkan opsi ganti ke Artikel, dan sebaliknya.
5. Redirect lama `admin.posts.index` (kalau masih dipakai di tempat lain,
   mis. link dashboard admin) diarahkan ke salah satu, atau dihapus kalau
   tidak ada yang mereferensikannya lagi — cek dulu dengan
   `grep -rn "admin.posts.index"` di seluruh project sebelum menghapus.

## 2. Perbaiki tampilan tombol pagination

Pagination yang dipakai sekarang (`{{ $galleries->links() }}` dkk di
`public/berita/index.blade.php`, `public/artikel/index.blade.php`,
`public/galeri/index.blade.php`) masih pakai view pagination bawaan
Laravel/Tailwind default — tidak match dengan desain situs (warna
teal-950/emas, border halus, dll dari `legacy-astro-site/Design.md`).

1. Publish view pagination Laravel ke project:
   `php artisan vendor:publish --tag=laravel-pagination`
   (atau buat Blade component pagination custom dari nol di
   `resources/views/components/pagination.blade.php` kalau publish bawaan
   terlalu ribet diutak-atik).
2. Restyle tombol pagination memakai token warna situs: tombol halaman
   aktif pakai `bg-teal-950 text-paper`, tombol tidak aktif
   `border border-line text-ink-soft hover:border-teal-bright/30`, tombol
   disabled (Previous di halaman 1 / Next di halaman terakhir) dibuat
   terlihat jelas tidak bisa diklik (`opacity-40 cursor-not-allowed`).
3. Pastikan perubahan ini otomatis berlaku ke semua halaman publik yang
   pakai pagination (Berita, Artikel) tanpa perlu edit tiap file — itu
   sebabnya diperbaiki di level view pagination, bukan per halaman.
4. **Galeri publik TIDAK memakai pagination sama sekali** (lihat poin 3
   di bawah) — jadi hapus `{{ $galleries->links() }}` dari
   `public/galeri/index.blade.php` sekalian kalau belum dihapus di poin 3.

## 3. Kembalikan tampilan Galeri publik seperti desain asli (bento + lightbox)

**Ini yang paling penting.** Implementasi Galeri publik sekarang
(`public/galeri/index.blade.php` + `public/galeri/show.blade.php`)
menyimpang dari desain asli:
- Sekarang: grid seragam `sm:grid-cols-2 lg:grid-cols-3`, klik cover album
  **pindah ke halaman baru** (`/galeri/{id}`) untuk lihat isinya.
- Desain asli (`legacy-astro-site/src/pages/galeri.astro`): **bento grid
  asimetris** (tiap kartu beda ukuran kolom/baris, lihat array `spans` di
  file itu), dan klik cover album **tidak pindah halaman** — langsung buka
  **lightbox** (`<dialog>` modal) yang menampilkan semua foto album itu
  dengan navigasi sebelumnya/berikutnya, caption, dan counter foto.

Kerjakan:

1. **Hapus rute per-album** `public.galeri.show` dan file
   `show.blade.php` — semua interaksi lihat album terjadi di 1 halaman
   `/galeri` lewat lightbox, bukan halaman terpisah.
2. Ubah `public/galeri/index.blade.php` meniru `galeri.astro` persis:
   - Grid asimetris pakai array `spans` yang sama (lihat kode aslinya):
     `['md:col-span-3 lg:col-span-5 lg:row-span-2', 'md:col-span-3 lg:col-span-7', 'md:col-span-2 lg:col-span-4', 'md:col-span-2 lg:col-span-3', 'md:col-span-2 lg:col-span-7', 'md:col-span-6 lg:col-span-5']`,
     diterapkan berulang (`index % 6`) ke tiap kartu album.
   - Tiap kartu jadi `<button type="button">` (bukan `<a>`), dengan atribut
     `data-lightbox-items` berisi JSON array seluruh foto di album itu:
     `[{src, alt, caption}, ...]` — ambil dari `$gallery->images` beserta
     URL media-nya (`getFirstMediaUrl('foto')`), bukan cuma cover.
   - Badge "N Foto" di pojok kanan atas kalau album punya lebih dari 1 foto
     (persis seperti aslinya).
   - Overlay teks nama album + keterangan di bagian bawah kartu.
3. Tambahkan markup `<dialog data-lightbox>` di bagian bawah halaman
   (persis strukturnya dari `galeri.astro`: tombol close, prev, next,
   `<figure data-lightbox-stage>` berisi `<img data-lightbox-image>`,
   caption, dan counter).
4. Port logic JS dari `legacy-astro-site/src/scripts/interactions.ts`
   (fungsi `initLightbox` dan `parseLightboxItems`) ke JS yang dipanggil
   lewat Vite di project Laravel — pakai `<dialog>.showModal()` native,
   dukung navigasi keyboard (panah kiri/kanan), klik di luar gambar untuk
   menutup, dan kunci scroll body saat lightbox terbuka
   (`document.body.classList.add('is-locked')`, sudah ada class ini di
   `global.css` lama — porting juga CSS terkait `.lightbox`,
   `.lightbox__figure`, `.lightbox__frame`, `.lightbox__image`, dan
   `.is-locked` dari `legacy-astro-site/src/styles/global.css`).
5. **Tidak ada pagination di halaman Galeri** — tampilkan semua album
   sekaligus seperti aslinya (lihat poin 2.4 di atas).
6. Update test: halaman `/galeri` mengembalikan 200 dan setiap kartu album
   punya atribut `data-lightbox-items` berisi JSON valid sejumlah foto
   yang benar. Hapus test lama yang menguji route `/galeri/{id}` kalau ada.
7. Bandingkan hasil akhir side-by-side dengan
   `legacy-astro-site/src/pages/galeri.astro` yang sudah di-render (atau
   dengan situs live sebelumnya kalau halaman ini pernah tayang) — target
   akhirnya terlihat identik, cuma datanya sekarang dari database bukan
   file Markdown.

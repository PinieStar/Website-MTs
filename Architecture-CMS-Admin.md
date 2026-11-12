# Architecture — CMS Admin Panel MTs NU Al Syairiyah

> Arsitektur teknis untuk migrasi dari Astro statis ke Laravel + Livewire dinamis, dengan panel admin untuk 4 modul: Berita & Artikel, Galeri, Prestasi.
> Filament **tidak dipakai**. Inertia.js **tidak dipakai**.

---

## 1. Gambaran Umum

```
                    ┌─────────────────────────────┐
                    │     Admin (browser)          │
                    │  login via Breeze             │
                    └───────────────┬───────────────┘
                                    │ HTTPS
                                    ▼
                    ┌─────────────────────────────┐
                    │        Laravel App            │
                    │  ┌─────────────────────────┐  │
                    │  │ Route group /admin       │  │
                    │  │ middleware: auth          │  │
                    │  │ Livewire components:      │  │
                    │  │  - PostManager (Berita &   │  │
                    │  │    Artikel)                │  │
                    │  │  - GalleryManager          │  │
                    │  │  - AchievementManager      │  │
                    │  └─────────────────────────┘  │
                    │  ┌─────────────────────────┐  │
                    │  │ Route publik (tanpa auth) │  │
                    │  │ Controller/Blade biasa:    │  │
                    │  │  - Beranda, Profil, dll     │  │
                    │  │    (statis seperti sebelum) │  │
                    │  │  - Berita/Artikel/Galeri/   │  │
                    │  │    Prestasi (baca dari DB)  │  │
                    │  └─────────────────────────┘  │
                    └───────┬───────────────┬─────────┘
                            │               │
                            ▼               ▼
                 ┌─────────────────┐  ┌─────────────────┐
                 │   MySQL/MariaDB   │  │ Storage (disk     │
                 │ posts, galleries,  │  │ public + symlink) │
                 │ gallery_images,    │  │ via Spatie Media  │
                 │ achievements,      │  │ Library           │
                 │ users              │  │                   │
                 └─────────────────┘  └─────────────────┘
                            ▲
                            │
                    ┌───────┴────────┐
                    │  Pengunjung     │
                    │  (publik, tanpa │
                    │  login)         │
                    └────────────────┘
```

Tidak ada API terpisah dan tidak ada frontend JS framework terpisah (Vue/React). Semua halaman — admin maupun publik — dirender server-side sebagai Blade, dengan interaktivitas tambahan dari Livewire di sisi admin.

## 2. Tumpukan Teknologi

| Lapisan | Pilihan | Alasan |
|---|---|---|
| Framework | **Laravel 13.x** (rilis Maret 2026) | Versi mayor stabil terkini — upgrade dari Laravel 12 tanpa breaking change berarti; ekosistem matang, ORM Eloquent, migration, validation bawaan |
| Interaktivitas admin | **Livewire 3** | Reaktif tanpa SPA/API terpisah; tetap pakai Blade + Tailwind yang sudah ada |
| Scaffolding auth | **Laravel Breeze** (stack Blade, *bukan* Vue/React/API) | Paling ringan untuk login/logout/reset password admin |
| UI kecil (modal, dropdown, toggle) | **Alpine.js** | Sudah ikut terpasang otomatis bersama Livewire |
| Styling | **Tailwind CSS v4** | Reuse token desain (warna teal-950, krem, emas) dari versi Astro agar brand konsisten |
| Rich text editor | **TipTap** | Modular, ringan, mudah dihubungkan ke Livewire via `wire:ignore` + Alpine |
| Upload & manajemen gambar | **Spatie Media Library** | Mengurus relasi 1-sampul (Post) dan banyak-foto (Galeri) tanpa kolom manual; otomatis generate thumbnail/conversion |
| Optimasi gambar | **Intervention Image** (lewat konversi Media Library) | Resize/kompres otomatis saat upload agar halaman publik tetap ringan |
| Slug otomatis | **Spatie Laravel Sluggable** | Slug judul berita/artikel konsisten dan anti-duplikat |
| Sitemap | **Spatie Laravel Sitemap** | Menggantikan `sitemap.xml.ts` versi Astro |
| Database | **MySQL 8 / MariaDB** (sesuai dukungan hosting) | Standar di hosting Laravel shared maupun VPS |
| Pagination | Bawaan Eloquent (`paginate()`) | Cukup untuk volume konten sekolah |

> Catatan: Laravel 13 menyertakan **Laravel AI SDK** sebagai paket first-party stabil. Paket ini **tidak dipakai** di Tahap 2 — tidak ada kebutuhan fitur AI di 4 modul CRUD ini — tapi disebut di sini sebagai catatan bila nanti ada kebutuhan Tahap 3 (mis. bantuan menulis ringkasan berita otomatis).

**Yang sengaja tidak dipakai:**
- **Filament** — terlalu berat (banyak abstraksi panel/plugin) untuk 4 modul CRUD sederhana; desain custom Tailwind yang sudah ada lebih mudah diterapkan langsung ke Livewire polos.
- **Inertia.js + Vue/React** — menambah build pipeline JS dan kompleksitas tanpa manfaat nyata, karena kebutuhan panel admin ini murni CRUD + rich text + upload gambar, yang sudah tercakup penuh oleh Livewire.

## 3. Strategi Rendering

- Halaman publik: render server-side biasa (Blade), **bukan SSG** seperti Astro — setiap request membaca dari database. Untuk performa, tambahkan:
  - Eager loading relasi (`with('media')`, dll.) agar tidak N+1 query.
  - Opsional: cache halaman daftar berita/galeri dengan `Cache::remember()` (invalidasi otomatis saat admin simpan/hapus data).
- Panel admin: Livewire component per modul, full-page component di balik route `/admin/...`.
- Tidak ada JavaScript framework di sisi publik (tetap vanilla/Alpine seperlunya), mempertahankan karakter ringan dari versi Astro untuk pengunjung.

## 4. Model Data

### 4.1 `posts` (Berita & Artikel digabung)

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| type | enum(`berita`,`artikel`) | Membedakan tampilan listing |
| judul | string | |
| slug | string, unique | Auto-generate dari judul (Sluggable) |
| ringkasan | text, nullable | Untuk kartu/listing |
| isi | longtext | HTML hasil TipTap |
| kategori | enum(`berita`,`pengumuman`,`kegiatan`), nullable | Khusus type=berita, mengikuti schema Astro lama |
| status | enum(`draft`,`publish`), default `draft` | Kontrol tayang |
| sematkan | boolean, default false | Tampil prioritas di beranda/listing |
| tanggal_publish | date | |
| penulis_id | FK → users.id, nullable | |
| timestamps | | created_at, updated_at |

- Gambar sampul: via Media Library, collection `sampul` (maks 1 file).

### 4.2 `galleries` + `gallery_images`

| Tabel | Kolom | Keterangan |
|---|---|---|
| `galleries` | id, album (string), keterangan (text, nullable), timestamps | Setara 1 "album kegiatan". **Tidak ada kolom `urutan`** — daftar album di halaman admin maupun publik selalu diurutkan otomatis berdasarkan `created_at` terbaru (album yang baru dibuat tampil paling atas), konsisten dengan Prestasi & Berita/Artikel yang juga tanpa field sortir manual |
| `gallery_images` | id, gallery_id (FK), urutan (int), timestamps | Tiap baris = 1 foto, file aktual disimpan via Media Library collection `foto` pada model `GalleryImage`. **Kolom `urutan` di sini TETAP ADA** (beda dengan tabel `galleries`) karena nama file foto tidak merepresentasikan apa-apa — urutan tampil foto di dalam 1 album tetap diatur manual oleh admin lewat drag-reorder |

### 4.3 `achievements` (Prestasi — tanpa gambar)

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | PK |
| judul | string | |
| peringkat | string | mis. "Juara 1", "Juara 2" |
| tingkat | enum(`madrasah`,`kecamatan`,`kabupaten`,`provinsi`,`nasional`) | |
| bidang | enum(`akademik`,`keagamaan`,`olahraga`,`seni`) | |
| tahun | year/int | |
| penyelenggara | string, nullable | |
| timestamps | | |

> **Keputusan final:** tabel `achievements` **tidak punya kolom `urutan`**.
> Tidak dibutuhkan field sortir manual — daftar selalu diurutkan otomatis
> (tahun terbaru → tingkat tertinggi → tiebreaker `id`). Ini juga berlaku
> untuk Berita & Artikel (`posts`): tidak ada kolom `urutan` di sana, sortir
> otomatis berdasarkan `tanggal_publish` (+ `sematkan` untuk yang ingin
> ditonjolkan).

### 4.4 `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id, name, email, password | — | Bawaan Breeze |
| role | enum(`super_admin`,`admin`), default `admin` | Menentukan akses menu "Manajemen Admin" |
| aktif | boolean, default true | Super Admin bisa menonaktifkan akun Admin tanpa menghapus datanya (riwayat post tetap tertaut) |
| timestamps | | |

- Minimal 1 baris `role=super_admin` wajib ada setelah `php artisan migrate --seed` (dibuat lewat `UserSeeder`, bukan dari form).
- Saat login, akun dengan `aktif=false` ditolak meski passwordnya benar (dicek di custom login validation atau event `Authenticated`).

### 4.5 Middleware & Otorisasi Role

- Middleware kustom `EnsureUserIsSuperAdmin` (atau pakai Laravel Gate `Gate::define('manage-admins', fn ($user) => $user->role === 'super_admin')`) diterapkan pada route group `/admin/manajemen-admin/*`.
- Route biasa (`/admin/posts`, `/admin/galleries`, `/admin/achievements`) tetap hanya butuh middleware `auth` — bisa diakses kedua role.
- Sidebar menu "Manajemen Admin" dirender kondisional di Blade (`@can('manage-admins')`) supaya Admin biasa tidak melihat menu yang tidak bisa diaksesnya.

## 5. Routing

```
# Publik
GET  /berita              -> daftar Post type=berita, status=publish
GET  /berita/{slug}        -> detail
GET  /artikel              -> daftar Post type=artikel
GET  /artikel/{slug}        -> detail
GET  /galeri                -> daftar Gallery
GET  /galeri/{id}            -> detail album + foto
GET  /prestasi               -> daftar Achievement (filter tingkat/bidang/tahun)

# Admin (middleware: auth, prefix /admin)
GET  /admin                    -> dashboard ringkas
GET  /admin/posts              -> list (Livewire component)
GET  /admin/posts/create       -> form create
GET  /admin/posts/{id}/edit     -> form edit
GET  /admin/galleries           -> list album
GET  /admin/galleries/{id}       -> kelola foto 1 album
GET  /admin/achievements         -> list + form (bisa inline modal, tanpa halaman terpisah karena simpel)
GET  /admin/manajemen-admin        -> list akun admin (khusus role=super_admin, middleware tambahan)
```

Aksi create/update/delete ditangani langsung oleh method di dalam Livewire component (bukan route POST terpisah), sesuai pola standar Livewire.

## 6. Keamanan & Validasi

- Semua route `/admin/*` dilindungi middleware `auth` (session-based, bawaan Breeze — bukan token API).
- CSRF protection otomatis dari Laravel (form Livewire sudah menangani ini bawaan).
- Validasi server-side di tiap Livewire component (`protected $rules` / method `rules()`), pesan error berbahasa Indonesia lewat `validationAttributes()`/custom messages.
- Validasi upload gambar: tipe mime (`jpg,jpeg,png,webp`), ukuran maksimum (mis. 5 MB sebelum kompresi), dimensi minimum opsional.
- Rate limiting default Laravel untuk halaman login.

## 7. Migrasi Data Lama

### 7.0 Arsipkan, Jangan Hapus, Project Astro Lama

Sebelum mulai migrasi, pindahkan seluruh folder project Astro lama (termasuk `.astro`, `.vercel`, `dist`, `src`, `public`, dan dokumen `PRD.md`/`Architecture.md`/`Design.md`/`Features.md`/`README.md` versi Astro) ke folder arsip terpisah, berdampingan dengan project Laravel baru:

```
/legacy-astro-site/   ← seluruh isi project Astro lama, dipindah utuh
    src/content/berita/...
    src/content/galeri/...
    src/content/achievement/...
    content.config.ts
    PRD.md, Architecture.md, Design.md, Features.md, README.md   (versi Astro)
/laravel-cms/           ← project Laravel baru (hasil Tahap 0–4)
```

Dokumen Markdown versi Astro **tidak dihapus** karena masih jadi acuan: `Design.md` lama untuk token warna/tipografi publik (dipakai ulang saat porting tampilan Blade), `content.config.ts` untuk skema front-matter yang jadi acuan parser di command import (7.1), dan `Architecture.md`/`Features.md`/`PRD.md`/`README.md` lama sebagai riwayat keputusan bila suatu saat dibutuhkan. Folder `/legacy-astro-site/` baru aman dihapus **setelah** migrasi data (7.1–7.4) diverifikasi sukses **dan** website Laravel sudah live menggantikan yang lama di produksi.

1. Tulis Artisan command khusus (mis. `php artisan import:legacy-content`) yang membaca file Markdown dari `/legacy-astro-site/src/content/berita`, `galeri`, `achievement` (path arsip, lihat 7.0 — bukan path project Laravel sendiri). **Sumber data yang benar adalah file `.md` di dalam `src/content/...`** — abaikan folder `.astro/collections/` (berisi `*.schema.json`, `data-store.json`, `dev.json`, dst.) karena itu cache hasil generate Astro, bukan data asli, dan bisa basi/tidak lengkap.
2. Parse front-matter (YAML) tiap file, acuan skemanya ikuti `/legacy-astro-site/src/content.config.ts`, → insert ke tabel terkait via Eloquent.
3. Untuk galeri, salin file foto fisik dari `/legacy-astro-site/public/images/...` lalu daftarkan ke Media Library (bukan hanya menyalin path string).
4. Jalankan dulu di environment lokal/staging, verifikasi hasil tampil benar di halaman publik, baru dijalankan di server produksi.
5. Simpan backup file Markdown asli (jangan dihapus) sampai migrasi dipastikan sukses.

## 8. Deployment

> **Catatan status proyek:** bagian ini mendeskripsikan tahap *nanti* saat
> website benar-benar dipindahkan ke server produksi. **Project saat ini
> masih dalam tahap pengembangan** — jangan ikuti langkah "Build asset" di
> tabel bawah dulu. Selama development, cukup jalankan `php artisan serve`
> (atau `composer run dev` yang menjalankan server + queue + Vite dev server
> sekaligus) dan `npm run dev` untuk Tailwind/Vite dengan hot-reload. Build
> produksi (`npm run build`) baru relevan ketika checklist deploy di bawah
> ini benar-benar mulai dikerjakan.

| Aspek | Catatan |
|---|---|
| Hosting | Butuh **PHP 8.3+** (syarat minimum Laravel 13 — naik dari 8.2 di Laravel 11/12), ekstensi umum Laravel, akses Composer & `php artisan`. **Cek dulu versi PHP yang didukung hosting cPanel saat ini** sebelum development — kalau masih mentok di PHP 8.1/8.2, wajib upgrade paket hosting atau pindah ke VPS kecil (lebih fleksibel untuk queue/cron bila dibutuhkan nanti) |
| Database | MySQL/MariaDB disediakan hosting |
| Storage simlink | `php artisan storage:link` wajib dijalankan setelah deploy agar gambar bisa diakses publik |
| Environment | `.env` terpisah per environment (local/staging/production), `APP_DEBUG=false` di produksi |
| Build asset | `npm run build` untuk compile Tailwind (Vite), hasil di-commit/upload sesuai alur deploy yang dipakai |

## 9. Performa

- Index database pada kolom yang sering difilter (`status`, `type`, `tanggal_publish`, `gallery_id`).
- Lazy-load gambar (`loading="lazy"`) di halaman publik, pertahankan konvensi dari versi Astro.
- Pagination di semua listing (publik maupun admin) agar query tetap ringan.
- Opsional ke depan: Laravel Octane bila trafik meningkat signifikan (tidak dibutuhkan di skala sekolah saat ini).

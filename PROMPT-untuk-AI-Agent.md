# Prompt — Build CMS Admin Panel (Laravel) untuk MTs NU Al Syairiyah

> Cara pakai: taruh file ini bersama 4 file lain (`PRD-CMS-Admin.md`, `Architecture-CMS-Admin.md`, `Features-CMS-Admin.md`, `Design-Admin-Panel.md`) di root project, lalu paste prompt di bawah ini ke Claude Code / OpenCode. Jalankan **per-tahap** (jangan minta semua modul sekaligus dalam satu prompt) agar hasilnya bisa direview bertahap.

---

## Prompt Tahap 0 — Setup Project

```
Baca file PRD-CMS-Admin.md, Architecture-CMS-Admin.md, Features-CMS-Admin.md,
dan Design-Admin-Panel.md di root project ini sebagai spesifikasi utama.

Tugas sekarang: setup project Laravel baru dengan stack berikut (JANGAN pakai
Filament, JANGAN pakai Inertia.js — ini keputusan final, lihat bagian 6
PRD-CMS-Admin.md untuk alasannya):

1. Laravel 13.x terbaru (syarat PHP 8.3+ — cek dulu `php -v` di environment
   sebelum `composer create-project laravel/laravel`, pastikan tidak dapat
   versi PHP di bawah 8.3).
2. Laravel Breeze stack Blade (bukan Vue/React/API) untuk auth. Catatan:
   starter kit resmi Laravel 13 menambahkan fitur team-based multi-tenancy
   bawaan (mirip Jetstream Teams) — JANGAN pakai starter kit itu untuk
   kebutuhan multi-role kita, karena konsep "team" tidak sama dengan
   role Super Admin/Admin yang kita butuhkan. Tetap pakai Breeze biasa lalu
   tambahkan kolom `role` manual sesuai Architecture-CMS-Admin.md bagian 4.4.
3. Livewire 3 + Alpine.js untuk interaktivitas panel admin.
4. **Tailwind CSS v4** (wajib — ini styling utama seluruh panel admin maupun
   tampilan publik, jangan diganti/dilewati). Pakai token warna yang
   didefinisikan di Design-Admin-Panel.md bagian 2 (Teal 950 #042F2E, Emas
   #C9A24B, Krem #F6F4EE, dst). Tambahkan sebagai custom theme lewat `@theme`
   di file CSS utama (bukan `tailwind.config.js` versi lama — Tailwind v4
   pakai konfigurasi CSS-first). Pastikan Vite + plugin `@tailwindcss/vite`
   terpasang.

   **MODE PENGEMBANGAN — JANGAN JALANKAN BUILD PRODUKSI DULU.** Project ini
   masih tahap development, jadi:
   - Pakai `npm run dev` (Vite dev server, hot-reload) atau `composer run dev`
     (script bawaan Laravel yang menjalankan `php artisan serve` + queue
     listener + `npm run dev` sekaligus) untuk kerja sehari-hari.
   - JANGAN jalankan `npm run build` (build produksi/minifikasi) di tahap
     ini — itu baru dilakukan nanti saat benar-benar deploy ke server
     produksi (lihat Architecture-CMS-Admin.md bagian 8, yang khusus
     membahas tahap deployment, bukan tahap sekarang).
   - Validasi "Tailwind jalan dengan benar" cukup dengan membuka halaman di
     browser lewat dev server dan memastikan styling tampil, bukan dengan
     menjalankan build produksi.
5. Laravel 13 menyertakan Laravel AI SDK sebagai paket resmi — **jangan
   install/aktifkan paket ini**, tidak dibutuhkan untuk scope Tahap 2
   (lihat catatan di Architecture-CMS-Admin.md).
6. Install paket: spatie/laravel-medialibrary, spatie/laravel-sluggable,
   spatie/laravel-sitemap.
7. Migration untuk tabel: users (tambah kolom `role` enum('super_admin','admin')
   default 'admin', dan `aktif` boolean default true — lihat Architecture-CMS-Admin.md
   bagian 4.4), posts, galleries, gallery_images, achievements — ikuti skema
   kolom persis seperti di Architecture-CMS-Admin.md bagian 4.1–4.4.
8. Buat UserSeeder yang membuat SATU akun dengan role='super_admin' memakai
   email & password dari environment variable (jangan hardcode kredensial di
   kode), sesuai catatan keamanan di Features-CMS-Admin.md bagian 4.
9. Buat Gate/middleware otorisasi role (lihat Architecture-CMS-Admin.md
   bagian 4.5): hanya role=super_admin yang boleh akses route
   /admin/manajemen-admin/*.
10. Jalankan migration + seeder, pastikan `php artisan storage:link` sudah
    dijalankan.

Setelah selesai, tunjukkan struktur folder yang dihasilkan dan ringkas
keputusan yang kamu ambil bila ada yang tidak eksplisit di dokumen.
```

## Prompt Tahap 1 — Modul Prestasi (contoh pola, paling sederhana)

```
Spesifikasi lengkap ada di Features-CMS-Admin.md bagian 3 (modul Achievement)
dan Architecture-CMS-Admin.md bagian 4.3.

Buatkan:
1. Model Achievement + migration (jika belum) sesuai kolom yang disebutkan.
2. Livewire component AchievementManager untuk /admin/achievements:
   - List dengan filter tingkat, bidang, tahun (lihat kriteria A-01).
   - Form create/edit TANPA upload gambar (lihat A-02) — field: judul,
     peringkat, tingkat (select), bidang (select), tahun, penyelenggara.
     TIDAK ADA field urutan — posisi di daftar otomatis dari tahun & tingkat.
   - Hapus dengan modal konfirmasi (pola global GA-04 di Features-CMS-Admin.md).
3. Terapkan styling mengikuti Design-Admin-Panel.md bagian 4.1 (tabel) dan
   4.2 (form satu kolom untuk form pendek).
4. Tambahkan menu "Prestasi" di sidebar admin (Design-Admin-Panel.md bagian 3),
   terlihat untuk kedua role (admin & super_admin).
5. Tulis Feature Test Laravel sederhana: admin bisa create/update/delete
   achievement; tamu (belum login) tidak bisa akses route admin.

Ini modul contoh — ikuti pola penamaan & struktur folder yang sama untuk
modul berikutnya (Post, Galeri).
```

## Prompt Tahap 1.5 — Tambal: Halaman Publik Prestasi (PUB-04 kelewat di Tahap 1)

> Ini perbaikan dari dokumen, bukan dari kerjaan kamu — Prompt Tahap 1 di atas
> lupa minta halaman publik, padahal `/berita`, `/artikel`, `/galeri` semua
> sudah dapat. Jalankan ini sebelum lanjut ke Tahap 6.

```
Route publik /prestasi belum ada (masih 404). Spesifikasi: PUB-04 di
Features-CMS-Admin.md dan Architecture-CMS-Admin.md bagian 5 (routing).

**Ini replikasi tampilan yang SUDAH LIVE, bukan desain baru.** Halaman
https://alsyairiyah.sch.id/prestasi/ sudah tayang dan desainnya final —
tiru persis (card badge peringkat+tingkat gaya "JUARA 2 · PROVINSI" dengan
badge emas, header hijau tua "PRESTASI TERCATAT" + filter "RENTANG TAHUN"
di atasnya). Acuan kode aslinya ada di
legacy-astro-site/src/pages/prestasi.astro — baca dan tiru strukturnya,
jangan menafsirkan ulang dari screenshot saja.

Buatkan:
1. Controller/route GET /prestasi yang menampilkan achievements
   (status tidak relevan di sini — achievement tidak punya draft/publish,
   tampilkan semua, urut otomatis: tahun terbaru dulu, lalu tingkat
   tertinggi. Tidak ada kolom urutan manual — tiebreaker terakhir pakai id).
2. Tampilan kartu identik dengan yang sudah live: badge peringkat + tingkat,
   judul, tahun, penyelenggara — TANPA gambar (lihat PUB-04).
3. Filter rentang tahun di halaman publik, SAMA seperti yang sudah tayang
   sekarang — cek legacy-astro-site/src/pages/prestasi.astro untuk logic
   filter aslinya, jangan dibuat filter baru yang berbeda.
4. Pagination bila jumlah data banyak (saat ini 58 record) — HANYA jika versi
   live saat ini juga punya pagination; kalau versi live menampilkan semua
   sekaligus tanpa pagination, pertahankan begitu.
5. Tambahkan test: GET /prestasi mengembalikan 200 dan menampilkan minimal
   1 judul prestasi yang ada di database.
```

## Prompt Tahap 2 — Modul Berita & Artikel (Post)

```
Spesifikasi lengkap: Features-CMS-Admin.md bagian 1 (P-01 s/d P-05) dan
Architecture-CMS-Admin.md bagian 4.1.

Buatkan:
1. Model Post (type enum berita/artikel) + migration sesuai skema.
2. Integrasikan Spatie Media Library untuk gambar sampul (collection "sampul",
   maks 1 file, validasi jpg/png/webp maks 5MB — lihat Features-CMS-Admin.md
   bagian 1, P-02).
3. Integrasikan TipTap sebagai editor isi artikel (toolbar terbatas: bold,
   italic, heading H2/H3, list, link, sisipkan gambar, undo/redo — lihat
   Features-CMS-Admin.md P-03 dan Design-Admin-Panel.md bagian 4.3). Gambar
   yang disisipkan di isi artikel diupload ke storage, bukan base64.
4. Livewire component PostManager untuk /admin/posts: list dengan filter
   type/status + search judul (P-01), form create/edit dua kolom
   (Design-Admin-Panel.md bagian 4.2), slug otomatis via Spatie Sluggable
   dengan anti-duplikat.
5. Toggle "Sematkan di beranda" (P-05).
6. Halaman publik /berita dan /artikel (PUB-01, PUB-02 di Features-CMS-Admin.md)
   yang hanya menampilkan Post status=publish.
7. Ikuti pola struktur dari modul Achievement yang sudah dibuat sebelumnya.
```

## Prompt Tahap 3 — Modul Galeri

```
Spesifikasi lengkap: Features-CMS-Admin.md bagian 2 (G-01 s/d G-04) dan
Architecture-CMS-Admin.md bagian 4.2.

Buatkan:
1. Model Gallery + GalleryImage + migration sesuai skema (tabel gallery_images
   terpisah, bukan array JSON, agar foto bisa dihapus/diurutkan satu per satu).
2. Livewire component GalleryManager:
   - List album (G-01).
   - Form create/edit album (G-02).
   - Upload banyak foto sekaligus ke satu album dengan WithFileUploads,
     preview thumbnail, progress indicator, pesan error per file jika
     format/ukuran salah (G-03, Design-Admin-Panel.md bagian 4.4).
   - Hapus foto individual tanpa hapus album; hapus album sekaligus semua
     fotonya dengan modal konfirmasi tegas (G-04).
3. Halaman publik /galeri (PUB-03).
```

## Prompt Tahap 4 — Modul Manajemen Admin (khusus Super Admin)

```
Spesifikasi lengkap: Features-CMS-Admin.md bagian 4 (UM-01 s/d UM-05) dan
Architecture-CMS-Admin.md bagian 4.5.

PENTING — baca catatan keamanan di akhir bagian 4 Features-CMS-Admin.md
sebelum mengerjakan ini: tidak boleh ada jalur apapun di UI/form yang bisa
membuat akun role=super_admin baru.

Buatkan:
1. Livewire component AdminUserManager untuk /admin/manajemen-admin, DIBATASI
   middleware/Gate khusus (hanya role=super_admin, akses oleh admin biasa
   harus menghasilkan 403 — UM-01).
2. Form tambah admin baru: role yang bisa dipilih dari form ini HANYA 'admin',
   tidak pernah 'super_admin' (UM-02).
3. Toggle aktif/nonaktif akun (UM-03) — akun nonaktif tidak bisa login
   walau password benar.
4. Fitur reset password admin lain oleh super admin (UM-04).
5. Hapus akun admin dengan modal konfirmasi tegas; pastikan konten
   (Post/Gallery/Achievement) yang pernah dibuat akun tsb TIDAK ikut
   terhapus (UM-05).
6. Super Admin tidak bisa menghapus/menonaktifkan akunnya sendiri (cegah
   terkunci dari sistem) — tambahkan validasi ini secara eksplisit.
7. Tambahkan menu "Manajemen Admin" di sidebar HANYA untuk role=super_admin
   (Design-Admin-Panel.md bagian 3 & 4.6, badge emas dengan ikon bintang).
8. Tulis Feature Test: admin biasa yang mengakses route ini mendapat 403;
   super admin tidak bisa menghapus akun sendiri.
```

## Prompt Tahap 5 — Migrasi Data Lama

```
Spesifikasi: Architecture-CMS-Admin.md bagian 7 (termasuk 7.0 soal lokasi
arsip project Astro lama — JANGAN hapus folder arsip ini, baca saja).

PRASYARAT: pastikan folder project Astro lama sudah dipindah utuh ke
/legacy-astro-site/ (berdampingan dengan project Laravel ini), sesuai
Architecture-CMS-Admin.md bagian 7.0. Kalau belum, minta pengguna
memindahkannya dulu sebelum lanjut.

Buatkan Artisan command `php artisan import:legacy-content` yang:
1. Membaca file Markdown lama di /legacy-astro-site/src/content/berita,
   /legacy-astro-site/src/content/galeri, /legacy-astro-site/src/content/achievement
   (format front-matter YAML, skema aslinya ada di
   /legacy-astro-site/src/content.config.ts — baca file itu dulu sebelum menulis
   parser, jangan menebak skemanya).
2. Insert ke tabel posts/galleries+gallery_images/achievements yang sesuai.
3. Untuk galeri, salin file foto fisik dari /legacy-astro-site/public/images/...
   ke storage project Laravel dan daftarkan ke Media Library, BUKAN hanya
   menyalin path string.
4. Jalankan dalam transaction DB — kalau satu file gagal diparse, jangan
   hentikan semua proses, tapi catat & laporkan di akhir command mana saja
   yang gagal dan kenapa.
5. Tulis command ini idempotent (aman dijalankan ulang tanpa duplikat data).

Jangan jalankan command ini otomatis — tunjukkan dulu hasilnya di environment
lokal/staging untuk direview sebelum dieksekusi di server produksi.
```

## Prompt Tahap 6 — Porting Layout & Halaman Statis (Beranda, Profil, Akademik, Kontak, 404)

> **INI BUKAN REDESIGN. Ini replikasi 1:1.** Tampilan yang sudah live dan
> dipublikasikan di https://alsyairiyah.sch.id/ adalah **final** dan TIDAK
> BOLEH diubah — bukan warna, bukan layout, bukan jarak antar elemen, bukan
> copy/teks, bukan animasinya. Yang berubah **hanya mesin di belakangnya**:
> dari Astro (file statis) menjadi Laravel/Blade (sebagian datanya dinamis
> dari database untuk 4 modul CMS — Berita, Artikel, Galeri, Prestasi), tapi
> HASIL AKHIR DI BROWSER harus terlihat identik dengan yang sekarang sudah
> tayang. Kalau ada keputusan desain yang ambigu di kode lama, pertahankan
> apa adanya, JANGAN "diperbaiki" atau diinterpretasi ulang — tujuannya
> pengunjung yang sudah biasa dengan situs ini tidak menyadari ada perubahan
> sama sekali di tampilan.

```
Acuan desain WAJIB dibaca dan ditiru persis, bukan sekadar referensi:
legacy-astro-site/src/components/, legacy-astro-site/src/layouts/
BaseLayout.astro, legacy-astro-site/src/pages/{index,profil,akademik,kontak,404}.astro,
legacy-astro-site/src/styles/global.css, dan dokumen legacy-astro-site/Design.md
(token warna/tipografi versi publik — BEDA dari Design-Admin-Panel.md yang
khusus panel admin, jangan tertukar).

Setelah selesai, bandingkan langsung side-by-side dengan
https://alsyairiyah.sch.id/ (situs yang sudah live) halaman per halaman.
Kalau ada perbedaan visual sekecil apapun yang bukan karena data (teks/foto
berita baru dari CMS), itu dianggap bug dan harus diperbaiki sebelum lanjut.

Buatkan versi Blade dari:
1. Layout utama (ganti BaseLayout.astro) — Navbar, Footer, ThemeToggle,
   WhatsAppButton, PageTransition sesuai komponen Astro yang ada di
   src/components/navigation/ dan src/components/ui/.
2. Halaman Beranda (index.astro → welcome.blade.php atau home.blade.php),
   pertahankan komposisi bento grid, StatCard, SectionHeader dsb — ini
   referensinya ada juga di file "Modern Bento Grid Madrasah Website
   Showcase.png" di root legacy-astro-site/.
3. Halaman Profil, Akademik, Kontak — konten statis (bukan dari database),
   boleh tetap hardcode di Blade kecuali ada instruksi lain.
4. Halaman 404 custom (pakai aset 404-students.png/404-students2.png yang
   sudah ada di public/images/404/).
5. Animasi (GSAP) dari src/scripts/lib/gsap.ts, animations.ts, interactions.ts,
   transitions.ts — porting logic-nya ke JS biasa yang di-load lewat Vite,
   tetap pertahankan efek yang sama (jangan dihilangkan hanya karena pindah
   framework).
6. Pastikan navigasi (navigation.ts/config) tetap mengarah ke route Laravel
   yang benar: Beranda, Profil, Akademik, Berita, Artikel, Galeri, Prestasi,
   Kontak.

Kerjakan dalam MODE DEV (`npm run dev` / `composer run dev`), JANGAN build
produksi — lihat catatan di Tahap 0.

Bandingkan hasil akhir dengan tampilan live legacy di
https://alsyairiyah.sch.id/ sebagai pembanding visual, bukan untuk disalin
mentah (karena versi live itulah desain aslinya).
```

## Prompt Tahap 7 — SEO & Sitemap

```
Acuan: legacy-astro-site/src/lib/seo.ts, legacy-astro-site/src/pages/
robots.txt.ts dan sitemap.xml.ts, serta Architecture-CMS-Admin.md
(paket spatie/laravel-sitemap sudah terpasang sejak Tahap 0).

Buatkan:
1. Meta tag SEO (title, description, og:image, canonical) per halaman,
   termasuk halaman dinamis (detail Berita/Artikel/Galeri) — porting logic
   dari seo.ts, bukan ditulis ulang dari nol tanpa acuan.
2. robots.txt (route atau file statis di public/).
3. Sitemap XML otomatis via spatie/laravel-sitemap, generate dari data
   Post (status=publish), Gallery, dan halaman statis. Prestasi ikut
   dimasukkan ke sitemap kalau PUB-04 (Tahap 1.5) sudah punya route publik.
4. Jalankan sebagai Artisan command terjadwal (`php artisan sitemap:generate`)
   — TAPI jangan didaftarkan ke scheduler otomatis dulu (masih tahap
   pengembangan), cukup bisa dijalankan manual untuk sekarang.
```

## Prompt Tahap 8 — Checklist Sebelum Go-Live

> Ini bukan prompt tunggal, tapi checklist yang diminta untuk divalidasi satu
> per satu sebelum folder `legacy-astro-site/` boleh dipertimbangkan untuk
> dihapus (lihat Architecture-CMS-Admin.md bagian 7.0 — jangan dihapus
> sebelum semua ini selesai):

```
Tolong jalankan/verifikasi dan laporkan hasilnya satu per satu:
1. Semua halaman publik (beranda, profil, akademik, berita, artikel, galeri,
   prestasi, kontak, 404) bisa diakses tanpa error di mode dev.
2. Full test suite lolos (seperti laporan Tahap 5: X tests, Y assertions).
3. Login admin & super admin berfungsi, termasuk pembatasan role
   (Tahap 4) — admin biasa tidak bisa akses /admin/manajemen-admin.
4. Jalankan `php artisan import:legacy-content --dry-run` sekali lagi untuk
   memastikan tidak ada file legacy baru yang terlewat sejak Tahap 5.
5. Cek environment variables produksi (.env.production) sudah lengkap:
   DB, APP_KEY, MAIL (jika dipakai), APP_DEBUG=false, APP_URL sesuai domain
   alsyairiyah.sch.id.
6. BARU setelah semua di atas lolos: ikuti Architecture-CMS-Admin.md bagian 8
   untuk proses build produksi (`npm run build`) dan deployment — ini kali
   pertama build produksi boleh dijalankan di proyek ini.
```

---

## Catatan Pemakaian

- Jalankan tahap demi tahap. Setelah tiap tahap, **jalankan aplikasi & cek manual** sebelum lanjut ke tahap berikutnya — jangan biarkan AI agent mengerjakan 5 tahap tanpa jeda review.
- Kalau AI agent mengusulkan sesuatu yang bertentangan dengan dokumen (misal mengusulkan Filament atau Inertia lagi), tegaskan ulang: *"Tidak, ikuti keputusan di PRD-CMS-Admin.md bagian 6 — tanpa Filament, tanpa Inertia."*
- Simpan README perubahan/keputusan yang diambil AI agent di luar dokumen asli (mis. `CHANGELOG-DEV.md`) supaya dokumen spesifikasi tetap bersih sebagai sumber kebenaran.
- **Jangan hapus** folder `/legacy-astro-site/` (lihat Architecture-CMS-Admin.md bagian 7.0) sampai migrasi data (Tahap 5) terverifikasi sukses dan website Laravel sudah live menggantikan yang lama — folder ini satu-satunya sumber data mentah berita/galeri/prestasi lama.

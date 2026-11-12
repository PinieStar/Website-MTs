# PRD — CMS Admin Panel MTs NU Al Syairiyah

> Product Requirements Document · Versi 1.0 · Tahap 2 (Migrasi dari Astro statis ke Laravel dinamis)
> Dokumen ini melengkapi `PRD.md` versi Astro sebelumnya. Yang berubah: konten berita, artikel, galeri, dan prestasi tidak lagi disimpan sebagai file Markdown statis, melainkan dikelola lewat panel admin berbasis database oleh penanggung jawab madrasah.
> Penanda `[ISI]` berarti keputusan/data belum final dan harus dikonfirmasi sebelum development dimulai.

---

## 1. Ringkasan

Website MTs NU Al Syairiyah yang sebelumnya dibangun statis dengan Astro + Tailwind akan ditulis ulang menggunakan **Laravel + Livewire + Tailwind**, dengan tambahan **panel admin** agar penanggung jawab sekolah (bukan developer) bisa membuat dan mengubah **Berita, Artikel, Galeri Foto, dan Prestasi** sendiri tanpa perlu edit kode atau deploy ulang. Tampilan publik (desain, warna, animasi) dipertahankan sepersis mungkin dari versi Astro — yang berubah adalah sumber datanya: dari file Markdown statis menjadi database MySQL yang diisi lewat form admin.

Filament **sengaja tidak dipakai** karena dianggap terlalu berat untuk kebutuhan 4 modul CRUD sederhana ini. Stack admin dibangun manual dengan Livewire supaya ringan dan mudah dikustom sesuai desain yang sudah ada.

## 2. Latar Belakang & Masalah

| Masalah (versi statis) | Dampak |
|---|---|
| Konten berita hanya embed dari Instagram, bukan artikel asli | Madrasah tidak punya kontrol penuh atas isi berita, tergantung platform pihak ketiga |
| Update galeri/prestasi butuh edit file Markdown + rebuild + upload ulang hasil build | Tidak bisa dilakukan oleh staf non-teknis |
| Tidak ada riwayat/draft — sekali file disimpan, langsung tayang setelah build | Tidak ada mekanisme tinjau sebelum publish |
| Galeri berupa array path foto di satu file, sulit ditambah/dikurangi satu per satu | Rawan salah path, tidak ada preview saat input |

## 3. Tujuan

1. Admin madrasah bisa membuat/mengubah/menghapus Berita, Artikel, Galeri, dan Prestasi sendiri lewat browser, tanpa bantuan developer.
2. Konten baru tayang di halaman publik **tanpa proses build/deploy** — langsung tersimpan ke database dan tampil.
3. Admin bisa menulis artikel dengan format kaya (bold, heading, daftar, gambar sisipan, tautan) lewat editor visual, bukan Markdown mentah.
4. Upload gambar dilakukan lewat form (drag & drop / pilih file), bukan upload manual ke folder server.
5. Mempertahankan tampilan publik, performa, dan identitas visual yang sudah dibangun di versi Astro.

### Indikator Keberhasilan

| Indikator | Target |
|---|---|
| Waktu admin mempublikasikan 1 berita baru (dari login sampai tayang) | ≤ 5 menit, tanpa bantuan developer |
| Waktu training admin baru memakai panel | ≤ 30 menit demo |
| Downtime/deploy ulang yang dibutuhkan untuk update konten | Nol — semua perubahan via panel admin |
| Validasi input | Admin tidak bisa menyimpan data yang membuat halaman publik error (field wajib tervalidasi) |

## 4. Pengguna Sasaran

| Persona | Kebutuhan utama | Perangkat |
|---|---|---|
| **Super Admin** (1 orang, biasanya kepala TU/operator utama) | Semua yang bisa dilakukan Admin, **plus** menambah/menonaktifkan akun Admin lain dan mereset passwordnya | Laptop |
| **Admin/Operator madrasah** (1–beberapa orang) | Input berita, artikel, foto kegiatan, data prestasi; tidak paham kode; **tidak bisa** mengelola akun admin lain | Laptop, kadang HP |
| **Pengunjung publik** | Membaca berita/artikel, melihat galeri & prestasi | Mayoritas HP |

Role dibatasi 2 tingkat saja (Super Admin & Admin) — bukan permission granular per modul (mis. "hanya boleh isi Prestasi saja"), karena kebutuhan saat ini cukup soal *siapa yang boleh menambah admin baru*, bukan pembatasan modul per orang.

## 5. Ruang Lingkup

### 5.1 Termasuk (Tahap 2)

- Login admin (Laravel Breeze, stack Blade — bukan API/SPA).
- **Dua role: Super Admin & Admin.** Super Admin bisa menambah akun Admin baru, menonaktifkan/menghapus akun Admin, dan mereset password Admin lain. Admin biasa hanya bisa mengelola profil & password dirinya sendiri, dan tidak melihat menu "Manajemen Admin".
- Minimal harus ada **1 akun Super Admin** yang dibuat lewat seeder saat instalasi awal (tidak boleh membuat Super Admin dari form publik manapun, demi keamanan).
- CRUD **Berita & Artikel** (digabung 1 model `Post` dengan field `type`): judul, slug otomatis, ringkasan, isi (rich text via TipTap), gambar sampul, kategori, status draft/publish, tanggal tayang, opsi "sematkan di beranda".
- CRUD **Galeri**: album (judul, keterangan) berisi banyak foto; admin bisa upload banyak foto sekaligus, hapus foto satu-satu, atur urutan.
- CRUD **Prestasi**: judul, peringkat/juara, tingkat (madrasah/kecamatan/kabupaten/provinsi/nasional), bidang (akademik/keagamaan/olahraga/seni), tahun, penyelenggara — **tanpa upload gambar** (sesuai desain kartu prestasi yang sudah ada).
- Halaman publik (Berita, Artikel detail, Galeri, Prestasi) membaca data dari database, menggantikan content collections Astro.
- Migrasi data lama (file Markdown galeri/prestasi/berita yang sudah ada) ke database lewat seeder satu kali jalan.
- Validasi form + pesan error berbahasa Indonesia yang jelas untuk admin non-teknis.

### 5.2 Tidak Termasuk (Tahap 2)

| Hal | Keterangan |
|---|---|
| Permission granular per modul (mis. admin X hanya boleh isi Prestasi, admin Y hanya Berita) | Ditunda — role saat ini hanya 2 tingkat (Super Admin/Admin), bukan per-modul |
| Komentar pengunjung di artikel | Tidak diminta |
| Multi-bahasa konten | Hanya Bahasa Indonesia |
| Notifikasi email/WhatsApp saat ada konten baru | Ditunda |
| Analytics dashboard di panel admin | Ditunda |
| Rewrite halaman statis lain (Profil, Akademik, Kontak) jadi dinamis | Tetap statis/Blade biasa dulu, kecuali diputuskan lain |
| **Modul Ekstrakurikuler** (Pramuka, PMR, Marching Band, Rebana, Voli, Sepak Takraw) jadi dinamis/CRUD admin | **Keputusan final: tetap statis**, dikonfirmasi langsung — bukan kelupaan. Porting ke Blade sebagai halaman statis biasa (sama seperti Profil/Akademik/Kontak), bukan bagian dari 4 modul CMS |

### 5.3 Ditunda ke Tahap 3

- Role & permission (mis. guru hanya bisa input prestasi siswanya).
- Scheduled publish (jadwalkan tayang di tanggal tertentu).
- Riwayat revisi/versi artikel.
- Pencarian full-text di halaman publik.

## 6. Keputusan Produk

| Keputusan | Alasan |
|---|---|
| **Role dibatasi 2 tingkat** (`super_admin`, `admin`) lewat satu kolom `role` di tabel `users`, bukan tabel `roles`/`permissions` terpisah (tanpa Spatie Permission) | Kebutuhan saat ini sederhana (cuma "siapa boleh kelola akun admin lain"); tabel permission terpisah baru dibutuhkan kalau nanti ada hak akses per-modul |
| **Super Admin dibuat lewat seeder**, bukan form pendaftaran publik | Mencegah siapa pun membuat akun Super Admin sendiri dari luar; akun Admin baru hanya bisa dibuat oleh Super Admin yang sudah login |
| **Berita + Artikel digabung jadi satu model `Post`** dengan kolom `type` (`berita`/`artikel`) | Field-nya sama persis (judul, isi, sampul, tanggal); memisah jadi 2 model hanya menambah duplikasi kode CRUD tanpa manfaat |
| **Tidak pakai Filament** | Terlalu berat untuk 4 modul sederhana; sulit disesuaikan dengan desain Tailwind kustom yang sudah ada |
| **Livewire, bukan Inertia+Vue/React** | Tidak perlu SPA terpisah; Livewire cukup reaktif untuk CRUD tanpa build pipeline JS tambahan |
| **TipTap untuk rich text**, bukan TinyMCE/CKEditor | Ringan, modular, cukup untuk kebutuhan artikel sekolah |
| **Spatie Media Library** untuk upload gambar | Mengurus relasi gambar (1 sampul vs banyak foto galeri) tanpa kode manual berulang |
| **Galeri dipecah jadi tabel `galleries` + `gallery_images`** (bukan array di 1 kolom seperti versi Markdown) | Admin bisa tambah/hapus foto satu-satu dengan aman, tidak rawan salah urutan array |

## 7. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Hosting shared cPanel lama mungkin tidak mendukung versi PHP/Composer yang dibutuhkan Laravel 13 (minimum **PHP 8.3**) | Cek dukungan PHP 8.3+ dan akses Composer/SSH di hosting sebelum development; siapkan opsi pindah ke VPS kecil bila perlu |
| Admin tidak terbiasa dengan rich text editor | Sediakan toolbar terbatas (bold, italic, heading, list, gambar, tautan saja) agar tidak membingungkan |
| Data lama (Markdown) hilang/rusak saat migrasi | Buat seeder/import script yang dites di environment lokal dulu sebelum dijalankan di server produksi, dan backup file asli sebelum migrasi |
| Folder project Astro lama terlanjur dihapus sebelum migrasi selesai | Pindahkan (bukan hapus) seluruh folder Astro lama ke `/legacy-astro-site/` sejak awal (lihat `Architecture-CMS-Admin.md` bagian 7.0); baru dihapus setelah migrasi terverifikasi **dan** situs Laravel sudah live di produksi |
| Upload gambar ukuran besar memperlambat halaman publik | Validasi ukuran file saat upload + resize/kompres otomatis (Intervention Image) sebelum disimpan |

## 8. Dependensi Antar Dokumen

- Detail teknis stack & struktur data → lihat `Architecture-CMS-Admin.md`.
- Rincian fitur per modul & kriteria penerimaan → lihat `Features-CMS-Admin.md`.
- Panduan visual panel admin → lihat `Design-Admin-Panel.md`.

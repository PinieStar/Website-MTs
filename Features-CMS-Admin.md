# Features — CMS Admin Panel MTs NU Al Syairiyah

> Spesifikasi fitur panel admin per modul. Setiap fitur punya kriteria penerimaan yang dapat diuji.
> Stack: Laravel + Livewire + Breeze + TipTap + Spatie Media Library. Tanpa Filament, tanpa Inertia.

---

## 0. Fitur Global (Admin)

### GA-01 Login Admin
- Form login (email + password) via Laravel Breeze, Blade biasa.
- Redirect ke `/admin` (dashboard) setelah berhasil login.
- **Penerimaan:** salah password menampilkan pesan error jelas; sesi tetap login sampai logout manual atau sesi kedaluwarsa.

### GA-02 Layout Admin (Sidebar + Topbar)
- Sidebar kiri: logo madrasah, menu navigasi (Dashboard, Berita & Artikel, Galeri, Prestasi), tombol Logout.
- Topbar: nama admin yang sedang login.
- Menu aktif ditandai jelas (highlight).
- **Penerimaan:** navigasi antar modul tidak reload penuh halaman (pakai `wire:navigate` Livewire); sidebar collapse di layar sempit (tablet).

### GA-03 Notifikasi Aksi (Flash Message/Toast)
- Setiap aksi simpan/hapus/update menampilkan notifikasi sukses atau gagal (Livewire dispatch event → toast Alpine).
- **Penerimaan:** notifikasi otomatis hilang setelah beberapa detik; tidak menumpuk jika aksi dilakukan berkali-kali cepat.

### GA-04 Konfirmasi Hapus
- Setiap tombol hapus (post/album/foto/prestasi) memicu dialog konfirmasi sebelum benar-benar menghapus data.
- **Penerimaan:** data tidak terhapus jika admin klik "Batal"; setelah "Hapus" data benar-benar hilang dan file media terkait ikut terhapus dari storage.

### GA-05 Dashboard Ringkas
- Halaman awal setelah login: jumlah total Berita, Artikel, Galeri, Prestasi; beberapa konten draft terakhir.
- **Penerimaan:** angka sesuai jumlah data riil di database, update otomatis tanpa cache basi.

---

## 1. Modul Berita & Artikel (`Post`)

### P-01 Daftar Post
- Tabel list dengan kolom: judul, type (berita/artikel), kategori (khusus berita), status (draft/publish), tanggal publish, aksi (edit/hapus).
- Filter: by type, by status, pencarian judul (search box, live search Livewire).
- Pagination.
- **Penerimaan:** filter & search bisa dikombinasi; hasil update real-time tanpa reload halaman penuh.

### P-02 Buat/Edit Post
- Form fields: Tipe (Berita/Artikel — radio/select), Judul (slug auto-generate, bisa diedit manual), Ringkasan (textarea pendek), Isi artikel (editor TipTap), Kategori (khusus type=berita: berita/pengumuman/kegiatan), Gambar sampul (upload, preview sebelum simpan), Tanggal tayang, Status (Draft/Publish), Sematkan di beranda (toggle).
- Validasi: judul wajib, isi wajib (minimal panjang tertentu), gambar sampul wajib untuk status Publish, format gambar jpg/png/webp maks 5 MB.
- **Penerimaan:** simpan sebagai Draft tidak tampil di halaman publik; ubah ke Publish langsung tampil tanpa deploy ulang; slug otomatis tidak bentrok dengan post lain (ditambah suffix angka bila duplikat).

### P-03 Editor Rich Text (TipTap)
- Toolbar terbatas: Bold, Italic, Heading (H2/H3), Bullet/Numbered List, Link, Sisipkan Gambar, Undo/Redo.
- Gambar yang disisipkan di dalam isi artikel diupload ke storage dan URL-nya otomatis masuk ke HTML (bukan base64).
- **Penerimaan:** hasil HTML dari editor tampil identik di halaman publik (tidak ada style bocor/rusak); admin awam bisa memformat teks tanpa tahu HTML.

### P-04 Hapus Post
- Hapus post + file sampul & gambar isi terkait (opsional: gambar yang sudah disisipkan di isi artikel tetap ada di storage kecuali dibersihkan manual — `[ISI: putuskan apakah perlu cleanup otomatis gambar inline]`).
- **Penerimaan:** post hilang dari listing publik seketika setelah dihapus.

### P-05 Sematkan di Beranda
- Toggle "Sematkan" menentukan post muncul di slot unggulan beranda/listing (maks N post tersemat, sisanya urut tanggal terbaru).
- **Penerimaan:** post tersemat selalu tampil di posisi atas terlepas dari tanggal publish.

---

## 2. Modul Galeri

### G-01 Daftar Album
- Tabel/grid list album: nama album, jumlah foto, cover (foto pertama), aksi (kelola/hapus).
- **Urutan tampil album otomatis berdasarkan terbaru dibuat** (`created_at` descending, album terbaru di atas) — tidak ada field urutan manual untuk album (beda dengan urutan foto di dalam album, lihat G-03). Sempat dipertimbangkan alfabet, tapi dibatalkan karena mengelompokkan nama-nama mirip (mis. banyak album berawalan "Upacara...") secara tidak kronologis.
- **Penerimaan:** jumlah foto di kolom akurat sesuai relasi database; album yang baru dibuat admin selalu muncul di posisi teratas tanpa perlu diatur manual.

### G-02 Buat/Edit Album
- Form: Nama album, Keterangan (opsional). **Tidak ada field "Urutan tampil"** — posisi album di daftar otomatis berdasarkan kapan album dibuat (terbaru di atas).
- **Penerimaan:** album baru langsung muncul di halaman publik Galeri setelah disimpan, otomatis di posisi paling atas.

### G-03 Kelola Foto dalam Album
- Upload banyak foto sekaligus (multi-file input / drag & drop) ke dalam 1 album.
- Preview thumbnail tiap foto setelah terupload.
- Hapus foto satu per satu tanpa menghapus album.
- Atur ulang urutan foto (drag-reorder sederhana, atau input nomor urut — `[ISI: pilih salah satu sesuai kompleksitas yang diinginkan]`).
- **Penerimaan:** upload banyak file sekaligus tidak gagal diam-diam — setiap file yang gagal (format/ukuran salah) ditampilkan errornya per file; foto yang dihapus benar-benar hilang dari storage, bukan cuma disembunyikan.

### G-04 Hapus Album
- Menghapus album sekaligus seluruh foto di dalamnya (dengan konfirmasi tegas, karena aksi ireversibel).
- **Penerimaan:** semua file foto terkait ikut terhapus dari storage, tidak menyisakan file yatim.

---

## 3. Modul Prestasi (`Achievement`)

### A-01 Daftar Prestasi
- Tabel list: judul, peringkat (badge warna emas seperti desain kartu publik), tingkat, bidang, tahun, penyelenggara, aksi.
- Filter: by tingkat, by bidang, by tahun (dropdown/select, bukan search teks karena datanya terstruktur).
- **Penerimaan:** filter kombinasi berfungsi; urutan default otomatis — tahun terbaru dulu, lalu tingkat tertinggi (Nasional→Madrasah). **Tidak ada field urutan manual.**

### A-02 Buat/Edit Prestasi
- Form sederhana **tanpa upload gambar** (sesuai desain kartu publik yang hanya menampilkan teks + badge): Judul, Peringkat (teks bebas, mis. "Juara 1"), Tingkat (select: Madrasah/Kecamatan/Kabupaten/Provinsi/Nasional), Bidang (select: Akademik/Keagamaan/Olahraga/Seni), Tahun (number/year picker), Penyelenggara (teks, opsional). **Tidak ada field "Urutan"** — posisi di daftar otomatis mengikuti tahun & tingkat.
- **Penerimaan:** form ini paling sederhana di antara 4 modul (tidak ada komponen upload, tidak ada field sortir manual), cocok jadi modul contoh pertama saat development.

### A-03 Hapus Prestasi
- Hapus dengan konfirmasi.
- **Penerimaan:** data hilang dari listing publik seketika.

---

## 4. Modul Manajemen Admin (khusus Super Admin)

> Menu ini **tidak muncul sama sekali** di sidebar untuk akun ber-role `admin`. Semua route di bawah dilindungi otorisasi tambahan di luar `auth` biasa.

### UM-01 Daftar Akun Admin
- Tabel: nama, email, role (badge: Super Admin / Admin), status aktif/nonaktif, tanggal dibuat, aksi.
- Super Admin bisa melihat dirinya sendiri di daftar ini tapi **tidak bisa menghapus/menonaktifkan akun sendiri** (mencegah terkunci dari sistem).
- **Penerimaan:** admin biasa yang mencoba akses URL `/admin/manajemen-admin` langsung diarahkan ditolak (403), bukan hanya disembunyikan menunya.

### UM-02 Tambah Admin Baru
- Form: Nama, Email, Password (+ konfirmasi), Role (default `admin`; Super Admin baru hanya bisa dibuat manual lewat seeder/tinker, **tidak** lewat form ini — lihat catatan keamanan di bawah).
- Setelah dibuat, akun bisa langsung dipakai login (tidak perlu verifikasi email di Tahap 2, kecuali diputuskan lain).
- **Penerimaan:** email wajib unik; password minimal mengikuti aturan default Laravel (`Password::defaults()`); akun baru langsung muncul di daftar.

### UM-03 Nonaktifkan / Aktifkan Kembali Admin
- Toggle status `aktif` tanpa menghapus data — post/galeri/prestasi yang pernah dibuat admin tersebut tetap tersimpan dan tertaut ke nama aslinya.
- **Penerimaan:** admin yang dinonaktifkan tidak bisa login lagi (walau password benar), tapi riwayat kontennya tidak hilang atau berubah jadi "anonim".

### UM-04 Reset Password Admin Lain
- Super Admin bisa set ulang password akun Admin (mis. saat admin lupa password dan tidak ada fitur reset via email aktif).
- **Penerimaan:** perubahan password langsung berlaku, admin yang bersangkutan harus login ulang dengan password baru.

### UM-05 Hapus Akun Admin
- Hapus permanen akun (beda dari nonaktifkan). Konfirmasi tegas (lihat GA-04), dan sistem harus menjelaskan konsekuensinya: konten yang pernah dibuat akun ini **tidak ikut terhapus**, hanya kehilangan atribusi penulis (`penulis_id` jadi null) atau tetap menampilkan nama sebagai arsip — `[ISI: pilih salah satu sesuai preferensi]`.
- **Penerimaan:** menghapus akun admin tidak pernah menghapus Post/Galeri/Prestasi yang sudah dipublikasikan.

> **Catatan keamanan penting:** tidak boleh ada jalur di aplikasi (form, API, seeder yang dijalankan otomatis saat deploy) yang membuat akun `role=super_admin` baru secara otomatis/publik. Super Admin tambahan (jika dibutuhkan) hanya dibuat manual oleh developer lewat `php artisan tinker` atau seeder khusus yang dijalankan sendiri, bukan lewat UI.

---

## 5. Halaman Publik (Konsumsi Data CMS)

### PUB-01 Berita & Pengumuman
- Mengganti sumber dari Instagram embed → menampilkan sampul + ringkasan + isi artikel asli dari database.
- Hanya menampilkan Post dengan `status=publish` dan `tanggal_publish <= hari ini`.
- **Penerimaan:** tampilan kartu & halaman detail tetap mengikuti desain visual yang sudah ada di versi Astro (lihat `Design.md` lama untuk token warna/komponen).

### PUB-02 Artikel
- Mirip Berita tapi listing/halaman terpisah untuk `type=artikel`.
- **Penerimaan:** sama seperti PUB-01.

### PUB-03 Galeri
- Menampilkan daftar album + grid foto per album, data dari `galleries`/`gallery_images`, bukan lagi array statis di frontmatter.
- **Penerimaan:** daftar album terurut dari yang paling baru dibuat; foto di dalam tiap album tetap terurut sesuai kolom `urutan` yang diatur admin lewat drag-reorder.

### PUB-04 Prestasi
- Menampilkan kartu prestasi (badge peringkat+tingkat, judul, tahun, penyelenggara) **tanpa gambar**, sesuai tangkapan layar desain yang sudah disetujui.
- **Penerimaan:** tampilan identik dengan desain existing, hanya sumber data yang berubah (DB, bukan Markdown).

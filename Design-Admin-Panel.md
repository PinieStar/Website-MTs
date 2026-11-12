# Design — Admin Panel MTs NU Al Syairiyah

> Panduan visual khusus panel admin (`/admin/*`). Berbeda tujuan dari `Design.md` versi publik: di sini prioritas adalah **kejelasan fungsional** untuk admin non-teknis, bukan daya tarik visual untuk pengunjung.
> Tetap memakai token warna & font yang sama dengan situs publik supaya brand konsisten, tapi komposisinya jauh lebih sederhana dan utilitarian.

---

## 1. Prinsip Desain Panel Admin

1. **Jelas sebelum indah.** Admin madrasah bukan desainer/developer — label, tombol, dan pesan error harus to the point, Bahasa Indonesia sederhana, tanpa jargon teknis.
2. **Konsisten dengan brand, bukan identik dengan halaman publik.** Pakai warna teal-950/emas yang sama sebagai aksen (sidebar, tombol utama), tapi layout tetap seperti dashboard pada umumnya (tabel, form, sidebar) — bukan bento grid artistik seperti landing page.
3. **Minim friksi untuk tugas berulang.** Admin akan input berita berkali-kali — form harus pendek, jelas field mana yang wajib, dan ada feedback instan (loading state, toast sukses/gagal).
4. **Cegah kesalahan, jangan cuma menampilkannya setelah terjadi.** Validasi real-time (misal: counter karakter, preview gambar sebelum upload final, konfirmasi sebelum hapus) lebih baik daripada pesan error setelah submit.

## 2. Reuse Token dari Desain Publik

| Token | Hex | Penggunaan di Admin |
|---|---|---|
| Teal 950 `#042F2E` | Sidebar background, tombol primer | |
| Teal 700 `#0F766E` | Hover tombol, link aktif | |
| Emas Madrasah `#C9A24B` | Badge status "Publish", aksen kecil (bukan dominan) | |
| Krem Kertas `#F6F4EE` | Background area konten utama (bukan putih polos) | |
| Putih Tulang `#FBFAF6` | Card/table container | |
| Tinta `#0B1F1E` | Teks utama | |
| Garis Halus `#E2DED2` | Border tabel & input | |
| Merah standar (baru, khusus admin) `#DC2626` | Tombol/badge hapus & status "Draft" perlu perhatian | |
| Hijau standar (baru, khusus admin) `#16A34A` | Badge status "Publish" / notifikasi sukses | |

> Catatan: merah & hijau status ditambahkan khusus untuk panel admin (tidak dipakai di halaman publik) karena admin butuh sinyal cepat "ini sudah tayang / masih draft / akan terhapus" — kejelasan fungsional lebih penting daripada kemurnian palet di area ini.

## 3. Layout

```
┌───────────┬─────────────────────────────────────────┐
│           │  Topbar: [Nama Halaman]      [Admin ▾]    │
│  Sidebar   ├─────────────────────────────────────────┤
│  (teal-950)│                                           │
│            │   Konten utama (krem #F6F4EE)              │
│  Logo      │   ┌───────────────────────────────────┐   │
│            │   │  Card putih tulang: tabel/form      │   │
│  Dashboard │   └───────────────────────────────────┘   │
│  Berita &  │                                           │
│   Artikel  │                                           │
│  Galeri    │                                           │
│  Prestasi  │                                           │
│  ─────────  │     (menu di atas: semua role)            │
│  Manajemen  │     (menu ini: hanya tampil kalau          │
│   Admin ⭐  │      role = Super Admin)                   │
│  ─────────  │                                           │
│  Logout    │                                           │
└───────────┴─────────────────────────────────────────┘
```

- Sidebar tetap (fixed) di desktop, collapse jadi hamburger di layar sempit (tablet admin).
- Tidak perlu dioptimalkan untuk HP kecil — asumsi admin bekerja dari laptop (sesuai persona di PRD).

## 4. Komponen

### 4.1 Tabel List (Berita/Artikel, Galeri, Prestasi)
- Header kolom jelas, baris zebra-striping tipis (`bg-bone`/`bg-paper` berselang) agar mudah dipindai.
- Kolom "Status" ditampilkan sebagai badge pil kecil: hijau untuk Publish, abu/kuning untuk Draft.
- Aksi per baris: ikon edit (pensil) + ikon hapus (tong sampah warna merah), bukan teks tombol panjang, untuk hemat ruang.
- Search box & filter dropdown di atas tabel, sejajar, bukan di sidebar terpisah.

### 4.2 Form Create/Edit
- Satu kolom (bukan grid 2 kolom rumit) untuk form pendek (Prestasi).
- Dua kolom untuk form panjang dengan editor (Post): kolom kiri lebih lebar untuk editor isi, kolom kanan sempit untuk metadata (status, tanggal, kategori, sampul) — pola umum CMS (mirip WordPress) yang sudah familiar secara visual bagi kebanyakan orang.
- Label field di atas input (bukan di samping), ukuran font label sedikit lebih kecil & warna `ink-soft` agar hierarki jelas.
- Field wajib ditandai tanda bintang kecil, bukan teks "(wajib)" berulang di tiap field.

### 4.3 Editor TipTap
- Toolbar sticky di atas area edit, ikon-ikon dari set yang sama (Lucide) dengan situs publik agar konsisten.
- Border halus mengelilingi area edit (`border-line`), padding cukup lega supaya tidak terasa sempit.
- Tombol "Sisipkan Gambar" di toolbar memicu file picker, bukan input URL manual (admin tidak akan punya URL gambar siap pakai).

### 4.4 Upload Gambar (Sampul & Galeri)
- Drag & drop area dengan border putus-putus (dashed) + teks "Tarik foto ke sini atau klik untuk pilih file".
- Preview thumbnail muncul segera setelah file dipilih, sebelum admin klik Simpan.
- Progress indicator sederhana saat upload berlangsung (terutama untuk multi-upload galeri).
- Pesan error per file (bukan generik) jika format/ukuran tidak sesuai, misal: *"foto3.png terlalu besar (8 MB), maksimal 5 MB."*

### 4.5 Modal Konfirmasi Hapus
- Modal sederhana: judul "Hapus [nama item]?", teks peringatan singkat bila aksi ireversibel (terutama untuk hapus album galeri), dua tombol: "Batal" (netral) dan "Ya, Hapus" (merah, tegas).

### 4.6 Badge Role & Status Akun (khusus modul Manajemen Admin)
- Badge "Super Admin" memakai aksen emas (`#C9A24B`) dengan ikon bintang kecil, agar berbeda jelas dari badge "Admin" (abu netral) — konsisten dengan prinsip "sinyal cepat" di bagian 4.1.
- Badge status akun: hijau "Aktif", abu "Nonaktif" — pola warna sama seperti badge status Draft/Publish supaya admin tidak perlu belajar dua sistem warna berbeda.
- Tombol "Tambah Admin Baru" ditonjolkan sebagai tombol primer teal (sama perlakuan seperti "Buat Berita Baru" di modul lain), bukan warna berbeda, supaya terasa sebagai bagian wajar dari panel, bukan fitur tersembunyi/rahasia.

### 4.7 Toast Notifikasi
- Muncul di pojok kanan atas, warna sesuai jenis pesan (hijau=sukses, merah=gagal), auto-hilang ±3 detik, bisa ditutup manual.

## 5. Tipografi

- Font tetap pakai pasangan yang sama dengan situs publik (lihat `Design.md` versi Astro) untuk judul/heading di dashboard, tapi body teks tabel/form boleh memakai ukuran lebih kecil & lebih "netral" (sans-serif standar) demi keterbacaan data padat — tidak perlu karakter dekoratif seperti di halaman publik.

## 6. Hal yang Dihindari di Panel Admin

| Hindari | Alasan |
|---|---|
| Animasi scroll/parallax ala halaman publik | Mengganggu fokus kerja berulang, tidak menambah fungsi |
| Istilah teknis di label ("slug", "enum", "media collection") | Admin bukan developer — pakai istilah awam ("Alamat halaman", "Jenis", "Foto") |
| Form sangat panjang dalam satu scroll tanpa pengelompokan | Kelompokkan jadi bagian jelas (mis. "Informasi Utama", "Gambar", "Pengaturan Tayang") |
| Konfirmasi hapus yang terlalu ringan (langsung hilang tanpa modal) | Admin non-teknis rawan salah klik; butuh jeda konfirmasi |

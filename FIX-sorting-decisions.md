# Koreksi: Keputusan Sorting (menggantikan FIX-urutan-achievements.md)

> File `FIX-urutan-achievements.md` yang diberikan sebelumnya **DIBATALKAN —
> abaikan isinya sepenuhnya, jangan dikerjakan.** Keputusan sudah berubah
> setelah dikonfirmasi ulang. Dokumen Architecture-CMS-Admin.md dan
> Features-CMS-Admin.md sudah diperbarui mengikuti keputusan final di bawah
> ini — baca ulang bagian yang relevan di kedua file itu sebelum mengerjakan.

---

## 1. Prestasi, Berita, Artikel — TIDAK ADA PERUBAHAN, sudah benar

Penghapusan kolom `urutan` dari tabel `achievements` yang sudah kamu lakukan
di Tahap 1.5 **sudah benar, jangan dikembalikan**. Keputusan final:

- `achievements`: tidak punya kolom `urutan`. Sortir otomatis: tahun terbaru
  → tingkat tertinggi → tiebreaker `id`. Form admin Prestasi tidak perlu
  field "Urutan" — **ini sudah seperti itu sekarang, tidak perlu disentuh**.
- `posts` (Berita & Artikel): memang dari awal tidak pernah punya kolom
  `urutan` — tidak ada yang perlu diubah di sini.

Tidak ada aksi yang perlu dikerjakan untuk bagian ini. Disebutkan di sini
hanya supaya jelas bahwa `FIX-urutan-achievements.md` sudah tidak berlaku.

## 2. Galeri (Album) — PERUBAHAN BARU (revisi — bukan alfabet)

> **Catatan revisi:** alfabet murni sempat dipertimbangkan tapi dibatalkan —
> dites dulu terhadap 11 nama album yang ada sekarang, hasilnya 5 album
> "Upacara..." numpuk di tengah daftar dan tidak merepresentasikan urutan
> kronologis kegiatan. Keputusan final: **urutan otomatis berdasarkan
> terbaru dibuat (`created_at` descending)** — konsisten dengan Prestasi
> (tahun terbaru) dan Berita/Artikel (`tanggal_publish` terbaru), yaitu
> **tidak ada field manual sama sekali di keempat modul**.

Modul Galeri (Tahap 3) sudah dibangun dengan field `urutan` manual untuk
tabel `galleries` (album). Ini perlu diubah:

**Album (`galleries`) diurutkan otomatis: yang paling baru dibuat tampil
paling atas (`created_at` descending). BUKAN manual, BUKAN alfabet.**
**Foto di dalam album (`gallery_images`) TETAP pakai urutan manual** —
jangan diubah, drag-reorder foto tetap seperti sekarang.

Langkah:

1. Buat migration baru yang menghapus kolom `urutan` dari tabel `galleries`
   SAJA (bukan dari `gallery_images` — tabel itu tetap pakai `urutan`,
   jangan disentuh).
2. Hapus `urutan` dari `$fillable` di `app/Models/Gallery.php`.
3. Ubah query list album (baik di admin `GalleryManager` maupun di
   `PublicGalleryController`) dari `->orderBy('urutan')` menjadi
   `->latest()` / `->orderByDesc('created_at')` (album terbaru di atas).
4. Hapus field "Urutan tampil" dari form create/edit album di
   `GalleryManager`/`gallery-manager.blade.php` — termasuk:
   - Properti `public string $urutan` yang terkait album (BUKAN yang
     terkait `gallery_images`/foto — itu nama variabel beda, pastikan tidak
     salah hapus; cek dulu scope-nya per method).
   - Validasi, pesan error, dan input field "Urutan tampil" di form album.
   - Kolom "Urutan" di tabel list album (bukan di tabel foto).
5. **Jangan ubah apa pun yang terkait `gallery_images.urutan`** (reorder
   foto dalam 1 album, tombol panah naik/turun, drag-reorder) — itu tetap
   dipertahankan persis seperti sekarang.
6. Update test yang relevan: list album terurut alfabet; reorder foto dalam
   album tetap berfungsi seperti sebelumnya (regresi).
7. Jalankan ulang `php artisan import:legacy-content` setelah migration
   diterapkan untuk memastikan data lama masih konsisten (seharusnya tidak
   ada perubahan data, hanya skema & query sorting yang berubah).

## 3. Ringkasan akhir sorting per modul

| Modul | Field manual `urutan`? | Cara sortir |
|---|---|---|
| Prestasi (`achievements`) | Tidak ada | Tahun terbaru → tingkat tertinggi |
| Berita & Artikel (`posts`) | Tidak ada | `tanggal_publish` terbaru (+ `sematkan` untuk prioritas) |
| Galeri — album (`galleries`) | **Tidak ada (baru dihapus)** | **Terbaru dibuat di atas (`created_at` desc)** |
| Galeri — foto dalam album (`gallery_images`) | **Tetap ada** | Manual, diatur admin lewat drag-reorder |

> Dengan ini, keempat modul (Prestasi, Berita, Artikel, Galeri-album) punya
> filosofi sorting yang sama: **tidak ada field urutan manual, semua
> otomatis berdasarkan data terbaru.** Hanya foto di dalam satu album yang
> tetap manual, karena itu memang butuh kontrol editorial (admin yang tahu
> foto mana sebaiknya tampil duluan dalam 1 album).

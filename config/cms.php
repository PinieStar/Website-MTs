<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Aturan Upload Gambar
    |--------------------------------------------------------------------------
    |
    | Berlaku untuk gambar sampul berita/artikel, gambar di dalam isi
    | artikel, dan foto galeri. Hanya JPG (format paling ringan untuk foto)
    | dan maksimal 2 MB per file supaya penyimpanan hosting tidak cepat penuh.
    | Ubah di sini saja jika aturannya perlu diganti.
    |
    */

    'image' => [
        'extensions' => ['jpg', 'jpeg'],
        'mime_types' => ['image/jpeg'],
        'max_kb' => 2048,
    ],

];

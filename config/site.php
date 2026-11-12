<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Situs Publik
|--------------------------------------------------------------------------
| Nilai di sini disalin 1:1 dari legacy-astro-site/src/config/site.ts dan
| navigation.ts agar tampilan situs publik tetap sama dengan versi Astro.
| Konten yang dikelola lewat CMS (Berita, Artikel, Galeri, Prestasi) tetap
| dibaca dari database, bukan dari berkas ini.
*/

return [
    'name' => 'MTs NU Al Syairiyah',
    'short_name' => 'MTs NU Al Syairiyah',
    'level' => 'Madrasah Tsanawiyah',
    'affiliation' => 'Nahdlatul Ulama',
    'place' => 'Limpung',
    'tagline' => 'Membentuk Generasi Islami yang Unggul',
    'description' => 'Website resmi MTs NU Al Syairiyah, Kabupaten Batang. Informasi profil madrasah, program akademik, prestasi, berita, galeri, dan kontak.',
    'locale' => 'id-ID',
    'alternate_names' => [
        'MTs NU Al Syairiyah Limpung',
        'Madrasah Tsanawiyah NU Al Syairiyah',
        'MTs NU Al Syairiyah',
    ],
    'keywords' => [
        'MTs NU Al Syairiyah',
        'MTs NU Al Syairiyah Limpung',
        'madrasah tsanawiyah Limpung',
        'MTs Limpung',
        'sekolah Islam Kecamatan Limpung',
        'madrasah Batang',
        'PPDB MTs NU Al Syairiyah',
        'pendidikan Islam Kecamatan Limpung, Kabupaten Batang',
    ],

    'ppdb_url' => 'https://ppdb.alsyairiyah.sch.id/',
    'instagram_url' => 'https://www.instagram.com/mtsnualsyairiyah',
    'instagram_handle' => '@mtsnualsyairiyah',
    'youtube_url' => 'https://www.youtube.com/@mtsnualsyairiyah',
    'youtube_handle' => '@mtsnualsyairiyah',
    'tiktok_url' => 'https://www.tiktok.com/@mtsnualsyairiyah',
    'tiktok_handle' => '@mtsnualsyairiyah',

    'contact' => [
        'address' => 'Jl. Raya Limpung - Tersono No.Km. 1,5, Plumbon, Kec. Limpung, Kabupaten Batang, Jawa Tengah 51271',
        'phone' => '+62 857-8638-8418',
        'email' => 'mtsnualsyairiyahlimpung@gmail.com',
        'whatsapp' => '+62 857-8638-8418',
        'hours' => 'Senin - Sabtu, 07.00 - 14.00 WIB',
        'maps_url' => 'https://www.google.com/maps/place/MTs+NU+Al+Syairiyah+Limpung/@-7.0195808,109.9290506,17z/data=!3m1!4b1!4m6!3m5!1s0x2e70153f2c554ae5:0x2daf1196a584a850!8m2!3d-7.0195861!4d109.9316255!16s%2Fg%2F11b7c1_x5x?entry=ttu&g_ep=EgoyMDI2MTAyOC4wIKXMDSoASAFQAw%3D%3D',
        'maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7446.721054690934!2d109.9284075306579!3d-7.020065005314642!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70153f2c554ae5%3A0x2daf1196a584a850!2sMTs%20NU%20Al%20Syairiyah%20Limpung!5e0!3m2!1sid!2sid!4v1793632401827!5m2!1sid!2sid',
    ],

    'social' => [
        'instagram' => 'https://www.instagram.com/mtsnualsyairiyah',
        'youtube' => 'https://www.youtube.com/@mtsnualsyairiyah',
        'tiktok' => 'https://www.tiktok.com/@mtsnualsyairiyah',
    ],

    'stats' => [
        'students' => 595,
        'teachers' => 32,
        'staff' => 8,
        'classes' => 21,
    ],

    'ppdb' => [
        'schedule' => 'Belum ada jadwal',
        'note' => 'Jadwal resmi akan diperbarui di situs PPDB. Silakan buka tautan untuk informasi terkini.',
    ],

    'hero' => [
        'eyebrow' => 'Madrasah Tsanawiyah',
        'title' => 'Membentuk Generasi Islami yang Unggul',
        'subtitle' => 'Berilmu, berakhlak mulia, dan siap menghadapi masa depan dengan nilai-nilai Islam.',
    ],

    'editorial' => [
        'tahun_berdiri' => '1986',
        'sejarah' => [
            'linimasa' => [
                [
                    'tahun' => '1986',
                    'teks' => 'Berdiri atas prakarsa KH. Sya’ir, Zamakhsari, Amin Syafi’i, Abdullah Effendi, dan lainnya dengan nama MTs NU Plumbon di bawah naungan Lembaga Pendidikan Ma\'arif NU, menempati gedung Madrasah Diniyah Desa Plumbon dengan fasilitas seadanya.',
                ],
                [
                    'tahun' => '2000',
                    'teks' => 'Masuk ke Yayasan Islam Al Syairiyah dan berganti nama menjadi MTs NU Al Syairiyah hingga kini.',
                ],
            ],
            'latar_belakang' => [
                'Membentuk manusia muslim yang bertakwa kepada Allah SWT.',
                'Ikut aktif membantu pemerintah dalam rangka mencerdaskan kehidupan bangsa, dengan memajukan pendidikan IPTEK, IMTAQ, keterampilan, dan kemandirian.',
                'Mengembangkan ajaran Islam dalam usaha membantu pemerintah di bidang pembinaan mental spiritual.',
                'Memberikan kesempatan pendidikan yang terjangkau bagi masyarakat.',
            ],
        ],
        'tonggak_sejarah' => [
            [
                'tahun' => '1986',
                'teks' => 'Madrasah berdiri dengan nama MTs NU Plumbon di bawah naungan Lembaga Pendidikan Ma’arif NU.',
            ],
            [
                'tahun' => '2000',
                'teks' => 'Bergabung dengan Yayasan Islam Al Syairiyah dan berganti nama menjadi MTs NU Al Syairiyah.',
            ],
        ],
        'visi' => '“Mewujudkan generasi muslim beriman, bertaqwa, berjiwa sosial, berwawasan IPTEK, Terampil dan Berprestasi”',
        'misi' => [
            'Menanamkan nilai keimanan dan ketakwaan serta akhlak mulia melalui pengamalan ajaran Islam.',
            'Menumbuhkembangkan nilai-nilai karakter dalam lingkungan madrasah.',
            'Membekali peserta didik dengan ilmu pengetahuan.',
            'Mengoptimalkan potensi akademik melalui proses pembelajaran dan bimbingan serta melaksanakan program pembelajaran yang aktif, inovatif, kreatif, efektif, dan menyenangkan.',
            'Membekali peserta didik agar menjadi terampil dan berprestasi.',
        ],
        'tujuan' => [
            'Menghasilkan lulusan dan tenaga kependidikan yang berwawasan Islami.',
            'Menghasilkan peserta didik dan lulusan sebagai generasi yang berakhlakul karimah.',
            'Mewujudkan tenaga kependidikan yang berakhlakul karimah.',
            'Menghasilkan mutu lulusan yang cerdas dan mampu bersaing secara global.',
            'Memberikan pendidikan sebagai bekal untuk menuju jenjang pendidikan tingkat atas.',
            'Mewujudkan tenaga kependidikan yang profesional.',
            'Menghasilkan lulusan dan tenaga kependidikan yang mampu membawa nama baik madrasah.',
            'Menghasilkan lulusan yang bersikap jujur dan bertanggung jawab dalam profesinya.',
            'Mewujudkan peserta didik yang kreatif, inovatif, dan inspiratif.',
        ],
        'programs' => [
            [
                'title' => 'Tahfidz & Tahsin Al-Qur\'an',
                'description' => "Program pembinaan Al-Qur'an untuk meningkatkan kefasihan membaca, ketepatan tajwid, serta menumbuhkan kecintaan peserta didik terhadap Al-Qur'an.",
            ],
            [
                'title' => 'KKO (Kelas Khusus Olahraga)',
                'description' => 'Program pengembangan bakat olahraga yang mendorong siswa untuk meraih prestasi melalui latihan dan pembinaan.',
            ],
            [
                'title' => 'KKNO (Kelas Khusus Non Olahraga)',
                'description' => 'Pembinaan akademik dan non-akademik melalui KSM, MTQ, Kaligrafi, Story Telling, Olimpiade, dan kompetisi lainnya.',
            ],
            [
                'title' => 'Penguatan Aswaja',
                'description' => 'Program penguatan nilai-nilai Ahlussunnah wal Jamaah melalui pembelajaran ke-NU-an, pembiasaan amaliyah, dan pembentukan akhlak mulia.',
            ],
            [
                'title' => 'Pembinaan Prestasi',
                'description' => 'Program pengembangan bakat dan kemampuan siswa untuk meraih prestasi di berbagai ajang perlombaan.',
            ],
            [
                'title' => 'Karakter Islami',
                'description' => 'Program pembinaan kepribadian Islami melalui pembiasaan ibadah dan penerapan akhlakul karimah dalam kehidupan sehari-hari.',
            ],
        ],
        'ekskul' => [
            [
                'nama' => 'Pramuka',
                'deskripsi' => 'Kegiatan pengembangan karakter melalui pendidikan kepramukaan untuk membentuk siswa yang mandiri, disiplin, dan bertanggung jawab.',
                'foto' => '/images/ekstra/pramuka1.jpg',
                'ikon' => 'sparkles',
                'urutan' => 1,
            ],
            [
                'nama' => 'PMR',
                'deskripsi' => 'Kegiatan pengembangan keterampilan kesehatan dan kepedulian sosial untuk membentuk siswa yang sigap, peduli, dan bertanggung jawab.',
                'foto' => '/images/ekstra/pmr.jpg',
                'ikon' => 'sparkles',
                'urutan' => 2,
            ],
            [
                'nama' => 'Marching Band',
                'deskripsi' => 'Kegiatan ekstrakurikuler yang menggabungkan seni musik, gerak, dan formasi untuk menciptakan penampilan yang harmonis.',
                'foto' => '/images/ekstra/marching-band.jpg',
                'ikon' => 'sparkles',
                'urutan' => 3,
            ],
            [
                'nama' => 'Rebana',
                'deskripsi' => 'Kegiatan pengembangan seni musik Islami untuk menumbuhkan kreativitas, kecintaan terhadap shalawat, dan nilai-nilai religius.',
                'foto' => '/images/ekstra/rebana.jpg',
                'ikon' => 'sparkles',
                'urutan' => 4,
            ],
            [
                'nama' => 'Voli',
                'deskripsi' => 'Kegiatan pengembangan keterampilan olahraga bola voli untuk meningkatkan kemampuan teknik, kebugaran, dan kerja sama tim.',
                'foto' => '/images/ekstra/voli.jpg',
                'ikon' => 'sparkles',
                'urutan' => 5,
            ],
            [
                'nama' => 'Sepak Takraw',
                'deskripsi' => 'Wadah pengembangan bakat olahraga melalui latihan teknik, ketangkasan, dan kerja sama dalam permainan sepak takraw.',
                'foto' => '/images/ekstra/sepak-takraw.jpg',
                'ikon' => 'sparkles',
                'urutan' => 6,
            ],
        ],
    ],

    'seo' => [
        'default_title' => 'MTs NU Al Syairiyah | Madrasah Islami Unggul di Batang',
        'title_separator' => '|',
        'default_description' => 'Website resmi MTs NU Al Syairiyah, Kabupaten Batang. Informasi profil madrasah, program akademik, prestasi, berita, galeri, dan kontak.',
        'default_image' => '/images/og-default.jpg',
        'image_width' => 1200,
        'image_height' => 630,
        'locale' => 'id_ID',
        'language' => 'id-ID',
        'geo' => [
            'latitude' => -7.0195861,
            'longitude' => 109.9316255,
            'region' => 'ID-JT',
            'placename' => 'Limpung, Kabupaten Batang, Jawa Tengah',
        ],
    ],

    'navigation' => [
        'primary' => [
            [
                'label' => 'Beranda',
                'route' => 'public.home',
                'active' => ['public.home'],
            ],
            [
                'label' => 'Profil',
                'route' => 'public.profil',
                'active' => ['public.profil'],
            ],
            [
                'label' => 'Akademik',
                'route' => 'public.akademik',
                'active' => ['public.akademik'],
            ],
            [
                'label' => 'Prestasi',
                'route' => 'public.prestasi.index',
                'active' => ['public.prestasi.*'],
            ],
            [
                'label' => 'Informasi',
                'route' => 'public.berita.index',
                'active' => ['public.berita.*', 'public.artikel.*', 'public.galeri.*'],
                'children' => [
                    [
                        'label' => 'Berita & Pengumuman',
                        'route' => 'public.berita.index',
                        'description' => 'Kabar terbaru madrasah',
                        'active' => ['public.berita.*'],
                    ],
                    [
                        'label' => 'Artikel',
                        'route' => 'public.artikel.index',
                        'description' => 'Gagasan dan tulisan madrasah',
                        'active' => ['public.artikel.*'],
                    ],
                    [
                        'label' => 'Galeri',
                        'route' => 'public.galeri.index',
                        'description' => 'Foto dan video kegiatan',
                        'active' => ['public.galeri.*'],
                    ],
                ],
            ],
            [
                'label' => 'Kontak',
                'route' => 'public.kontak',
                'active' => ['public.kontak'],
            ],
        ],
        'footer' => [
            ['label' => 'Profil', 'route' => 'public.profil'],
            ['label' => 'Akademik', 'route' => 'public.akademik'],
            ['label' => 'Prestasi', 'route' => 'public.prestasi.index'],
            ['label' => 'Berita', 'route' => 'public.berita.index'],
            ['label' => 'Artikel', 'route' => 'public.artikel.index'],
            ['label' => 'Galeri', 'route' => 'public.galeri.index'],
            ['label' => 'Kontak', 'route' => 'public.kontak'],
        ],
    ],
];

<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class PostSeeder extends Seeder
{
    /**
     * Palet warna sampul dummy agar setiap kartu punya gambar yang berbeda.
     *
     * @var list<array{from: array{int, int, int}, to: array{int, int, int}, accent: array{int, int, int}}>
     */
    private const COVER_PALETTES = [
        ['from' => [12, 74, 70], 'to' => [15, 118, 110], 'accent' => [201, 162, 77]],
        ['from' => [17, 94, 89], 'to' => [56, 143, 122], 'accent' => [231, 214, 170]],
        ['from' => [8, 47, 73], 'to' => [14, 116, 144], 'accent' => [198, 160, 84]],
        ['from' => [64, 46, 28], 'to' => [122, 84, 44], 'accent' => [230, 205, 150]],
        ['from' => [30, 51, 46], 'to' => [74, 124, 108], 'accent' => [212, 175, 96]],
        ['from' => [36, 48, 78], 'to' => [64, 96, 132], 'accent' => [213, 187, 130]],
        ['from' => [18, 60, 52], 'to' => [37, 106, 88], 'accent' => [224, 190, 116]],
        ['from' => [52, 43, 71], 'to' => [96, 75, 116], 'accent' => [220, 192, 140]],
    ];

    /**
     * Isi data berita dummy.
     *
     * @var list<array<string, mixed>>
     */
    private const BERITA = [
        [
            'judul' => 'Peringatan Maulid Nabi Muhammad SAW 1448 H Berlangsung Khidmat',
            'kategori' => 'kegiatan',
            'hari' => 3,
            'sematkan' => true,
            'ringkasan' => 'Seluruh siswa, guru, dan tenaga kependidikan mengikuti peringatan Maulid Nabi di halaman madrasah dengan rangkaian shalawat, khataman, dan tausiyah.',
            'isi' => [
                ['p' => 'MTs NU Al Syairiyah menggelar peringatan Maulid Nabi Muhammad SAW 1448 H di halaman madrasah. Kegiatan diikuti seluruh siswa, guru, tenaga kependidikan, pengurus yayasan, serta sejumlah wali murid.'],
                ['p' => 'Rangkaian acara diawali dengan pembacaan shalawat bersama, khataman Al-Qur\'an oleh perwakilan kelas, dan penampilan rebana dari ekstrakurikuler madrasah. Suasana pagi yang sejuk membuat seluruh peserta mengikuti jalannya acara dengan tertib.'],
                ['h2' => 'Meneladani Akhlak Rasulullah'],
                ['p' => 'Dalam tausiyahnya, penceramah mengajak para siswa untuk meneladani akhlak Rasulullah SAW dalam kehidupan sehari-hari, terutama dalam menjaga adab kepada guru, orang tua, dan teman sebaya.'],
                ['img' => '/images/gedung.jpg', 'alt' => 'Halaman MTs NU Al Syairiyah saat kegiatan berlangsung'],
                ['p' => 'Acara ditutup dengan doa bersama dan ramah tamah. Panitia berharap peringatan Maulid tahun ini tidak berhenti pada seremoni, tetapi benar-benar menumbuhkan kecintaan siswa kepada Rasulullah SAW.'],
            ],
        ],
        [
            'judul' => 'Siswa MTs NU Al Syairiyah Raih Juara 1 Lomba Rebana Tingkat Kabupaten',
            'kategori' => 'berita',
            'hari' => 9,
            'sematkan' => true,
            'ringkasan' => 'Tim rebana madrasah berhasil menempati posisi pertama pada lomba seni Islami tingkat Kabupaten Batang yang diikuti belasan madrasah.',
            'isi' => [
                ['p' => 'Tim ekstrakurikuler rebana MTs NU Al Syairiyah berhasil meraih juara 1 pada lomba seni Islami tingkat Kabupaten Batang. Lomba diikuti oleh belasan madrasah dan sekolah dari berbagai kecamatan.'],
                ['p' => 'Penampilan tim dinilai paling harmonis, baik dari segi vokal, ritme, maupun kekompakan formasi. Para juri juga mengapresiasi penghayatan syair shalawat yang dibawakan.'],
                ['h2' => 'Latihan Rutin Setiap Pekan'],
                ['p' => 'Pembina ekstrakurikuler rebana menyampaikan bahwa prestasi ini merupakan hasil latihan rutin setiap pekan. Ia berharap capaian tersebut memotivasi siswa lain untuk terus mengembangkan bakat di bidang seni Islami.'],
                ['p' => 'Kepala madrasah mengucapkan terima kasih kepada seluruh tim, pembina, dan wali murid yang telah mendukung persiapan lomba. Prestasi ini akan menjadi catatan tersendiri pada halaman prestasi madrasah.'],
            ],
        ],
        [
            'judul' => 'Pengumuman: Jadwal Penilaian Tengah Semester Ganjil 2026/2027',
            'kategori' => 'pengumuman',
            'hari' => 12,
            'sematkan' => false,
            'ringkasan' => 'Penilaian Tengah Semester Ganjil dilaksanakan selama satu pekan penuh. Siswa diharapkan mempersiapkan diri dan membawa perlengkapan ujian.',
            'isi' => [
                ['p' => 'Diberitahukan kepada seluruh siswa MTs NU Al Syairiyah bahwa Penilaian Tengah Semester (PTS) Ganjil tahun pelajaran 2026/2027 akan dilaksanakan mulai pekan depan.'],
                ['h2' => 'Ketentuan Peserta'],
                ['ul' => [
                    'Siswa wajib hadir paling lambat 15 menit sebelum ujian dimulai.',
                    'Membawa alat tulis lengkap dan kartu peserta ujian.',
                    'Mengenakan seragam madrasah sesuai ketentuan.',
                    'Tidak diperkenankan membawa telepon genggam ke dalam ruang ujian.',
                ]],
                ['p' => 'Jadwal lengkap per mata pelajaran dapat dilihat pada papan pengumuman madrasah dan grup koordinasi kelas. Perubahan jadwal, apabila ada, akan diinformasikan melalui wali kelas.'],
                ['p' => 'Orang tua diharapkan ikut mendampingi siswa belajar di rumah agar dapat mengikuti penilaian dengan optimal.'],
            ],
        ],
        [
            'judul' => 'Peringatan Hari Santri Nasional 2026 Diisi dengan Upacara dan Doa Bersama',
            'kategori' => 'kegiatan',
            'hari' => 18,
            'sematkan' => false,
            'ringkasan' => 'Peringatan Hari Santri dilaksanakan dengan upacara bendera, pembacaan doa, dan pesan tentang peran santri dalam menjaga nilai keislaman.',
            'isi' => [
                ['p' => 'MTs NU Al Syairiyah memperingati Hari Santri Nasional 2026 dengan upacara bendera di halaman madrasah. Seluruh siswa dan tenaga kependidikan mengikuti kegiatan dengan khidmat.'],
                ['p' => 'Dalam amanatnya, pembina upacara mengingatkan bahwa semangat Hari Santri bukan hanya milik pesantren, tetapi juga milik madrasah yang tumbuh dari tradisi keilmuan dan keagamaan yang sama.'],
                ['h2' => 'Santri yang Berilmu dan Berakhlak'],
                ['p' => 'Para siswa diajak untuk menjaga adab, disiplin, dan semangat belajar sebagai bentuk penghormatan kepada para kiai dan guru yang telah mewariskan ilmu.'],
                ['p' => 'Kegiatan ditutup dengan pembacaan doa bersama untuk para pendiri madrasah dan seluruh keluarga besar Yayasan Islam Al Syairiyah.'],
            ],
        ],
        [
            'judul' => 'Gerakan Jumat Sehat: Senam Bersama Seluruh Warga Madrasah',
            'kategori' => 'kegiatan',
            'hari' => 25,
            'sematkan' => false,
            'ringkasan' => 'Kegiatan senam pagi rutin digelar setiap Jumat untuk menjaga kebugaran siswa, guru, dan tenaga kependidikan.',
            'isi' => [
                ['p' => 'Seluruh warga MTs NU Al Syairiyah mengikuti senam bersama di halaman madrasah pada Jumat pagi. Kegiatan ini menjadi bagian dari pembiasaan hidup sehat yang rutin dilaksanakan.'],
                ['p' => 'Dipandu instruktur dari ekstrakurikuler olahraga, peserta mengikuti gerakan pemanasan, senam irama, dan pendinginan. Suasana berlangsung riang dan santai.'],
                ['p' => 'Pihak madrasah berharap pembiasaan ini tidak hanya menjaga kebugaran, tetapi juga mempererat kebersamaan antara siswa, guru, dan tenaga kependidikan.'],
            ],
        ],
        [
            'judul' => 'Kunjungan Studi Tiru dari MTs Negeri 2 Batang',
            'kategori' => 'berita',
            'hari' => 33,
            'sematkan' => false,
            'ringkasan' => 'Rombongan MTs Negeri 2 Batang berkunjung untuk mempelajari pengelolaan program tahfidz, KKO, dan tata kelola madrasah.',
            'isi' => [
                ['p' => 'MTs NU Al Syairiyah menerima kunjungan studi tiru dari rombongan MTs Negeri 2 Batang. Kunjungan diikuti kepala madrasah, wakil kepala, serta sejumlah guru pendamping.'],
                ['p' => 'Dalam pertemuan tersebut, kedua pihak berdiskusi tentang pengelolaan program tahfidz, Kelas Khusus Olahraga (KKO), pembinaan prestasi, dan tata kelola administrasi madrasah.'],
                ['h2' => 'Saling Belajar Antar Madrasah'],
                ['p' => 'Kepala MTs NU Al Syairiyah menyampaikan bahwa kunjungan semacam ini menjadi ruang saling belajar antar madrasah. Setiap madrasah memiliki kekuatan dan tantangan yang dapat dibagikan.'],
                ['p' => 'Rombongan kemudian diajak berkeliling melihat sarana belajar, ruang praktik, dan kegiatan ekstrakurikuler yang sedang berlangsung.'],
            ],
        ],
        [
            'judul' => 'Pelatihan Literasi Digital untuk Guru dan Tenaga Kependidikan',
            'kategori' => 'berita',
            'hari' => 40,
            'sematkan' => false,
            'ringkasan' => 'Guru dilatih memanfaatkan perangkat digital untuk pembelajaran, pengelolaan bahan ajar, dan penguatan literasi siswa.',
            'isi' => [
                ['p' => 'Sebanyak 40 guru dan tenaga kependidikan MTs NU Al Syairiyah mengikuti pelatihan literasi digital di aula madrasah. Pelatihan berlangsung selama dua hari dengan pendampingan praktik langsung.'],
                ['p' => 'Materi yang dibahas meliputi pemanfaatan perangkat pembelajaran digital, penyusunan bahan ajar interaktif, serta cara memverifikasi informasi sebelum dibagikan kepada siswa.'],
                ['h2' => 'Digital untuk Pembelajaran'],
                ['p' => 'Narasumber menekankan bahwa teknologi harus menjadi alat bantu, bukan pengganti peran guru. Guru tetap menjadi teladan dalam berpikir kritis dan beretika di ruang digital.'],
                ['p' => 'Peserta diminta menyusun rencana penerapan hasil pelatihan pada pembelajaran di kelas masing-masing. Hasilnya akan dibagikan pada forum refleksi guru pekan berikutnya.'],
            ],
        ],
        [
            'judul' => 'Pekan Olahraga Madrasah 2026 Resmi Dibuka',
            'kategori' => 'kegiatan',
            'hari' => 47,
            'sematkan' => false,
            'ringkasan' => 'Berbagai cabang olahraga dipertandingkan antar kelas dalam Pekan Olahraga Madrasah yang berlangsung meriah.',
            'isi' => [
                ['p' => 'Pekan Olahraga Madrasah 2026 resmi dibuka di lapangan MTs NU Al Syairiyah. Kegiatan diikuti perwakilan kelas dengan berbagai cabang pertandingan, mulai dari voli, sepak takraw, hingga lari estafet.'],
                ['p' => 'Pembukaan ditandai dengan defile kontingen kelas dan penampilan marching band madrasah. Sorak pendukung dari setiap kelas menambah semarak suasana.'],
                ['h2' => 'Menjaga Sportivitas'],
                ['p' => 'Kepala madrasah berpesan agar seluruh peserta menjunjung sportivitas dan menjadikan pertandingan sebagai sarana mempererat persaudaraan, bukan sekadar mengejar kemenangan.'],
                ['p' => 'Pekan Olahraga berlangsung selama tiga hari dan ditutup dengan pembagian hadiah bagi kelas dengan perolehan poin terbanyak.'],
            ],
        ],
        [
            'judul' => 'Penyerahan Bantuan Beasiswa bagi Siswa Berprestasi',
            'kategori' => 'berita',
            'hari' => 55,
            'sematkan' => false,
            'ringkasan' => 'Sejumlah siswa berprestasi menerima bantuan beasiswa dari yayasan sebagai bentuk dukungan terhadap keberlanjutan belajar.',
            'isi' => [
                ['p' => 'Yayasan Islam Al Syairiyah menyerahkan bantuan beasiswa kepada sejumlah siswa berprestasi MTs NU Al Syairiyah. Penyerahan dilakukan di aula madrasah dan disaksikan wali murid.'],
                ['p' => 'Beasiswa diberikan kepada siswa yang memiliki capaian akademik maupun non-akademik, serta menunjukkan kedisiplinan dan akhlak yang baik selama mengikuti pembelajaran.'],
                ['p' => 'Pihak yayasan berharap bantuan ini dapat meringankan beban keluarga sekaligus memotivasi siswa untuk terus berprestasi dan menjaga amanah yang diberikan.'],
            ],
        ],
        [
            'judul' => 'Praktik Manasik Haji Siswa Kelas IX Digelar di Aula Madrasah',
            'kategori' => 'kegiatan',
            'hari' => 63,
            'sematkan' => false,
            'ringkasan' => 'Siswa kelas IX mengikuti praktik manasik haji untuk memperkuat pemahaman terhadap tata cara ibadah haji dan makna spiritualnya.',
            'isi' => [
                ['p' => 'Siswa kelas IX MTs NU Al Syairiyah mengikuti praktik manasik haji di aula madrasah. Kegiatan ini merupakan bagian dari penguatan materi fikih ibadah.'],
                ['p' => 'Aula ditata menyerupai area Masjidil Haram dan Arafah. Para siswa mempraktikkan tahapan ibadah haji, mulai dari ihram, tawaf, sai, wukuf, hingga melempar jumrah.'],
                ['h2' => 'Belajar Makna Ibadah'],
                ['p' => 'Guru pendamping menjelaskan bahwa manasik bukan sekadar hafalan urutan gerakan, tetapi juga latihan memahami nilai kesabaran, kebersamaan, dan ketaatan kepada Allah SWT.'],
                ['p' => 'Kegiatan berlangsung dengan tertib dan penuh antusiasme. Siswa mengaku lebih mudah memahami materi setelah mempraktikkannya secara langsung.'],
            ],
        ],
        [
            'judul' => 'Penerimaan Rapor Tengah Semester Bersama Orang Tua Siswa',
            'kategori' => 'pengumuman',
            'hari' => 70,
            'sematkan' => false,
            'ringkasan' => 'Penerimaan rapor tengah semester dilaksanakan pada hari Sabtu di ruang kelas masing-masing dengan pendampingan wali kelas.',
            'isi' => [
                ['p' => 'Diberitahukan kepada seluruh orang tua atau wali siswa bahwa penerimaan rapor tengah semester akan dilaksanakan pada hari Sabtu pekan ini di ruang kelas masing-masing.'],
                ['p' => 'Acara diawali dengan pertemuan singkat bersama wali kelas, dilanjutkan dengan pembagian rapor dan konsultasi perkembangan belajar siswa. Orang tua diharapkan hadir tepat waktu.'],
                ['p' => 'Apabila berhalangan hadir, mohon menghubungi wali kelas terlebih dahulu agar dapat dijadwalkan pengambilan rapor pada waktu lain.'],
            ],
        ],
        [
            'judul' => 'Pengumuman Libur Hari Raya Idul Adha 1447 H',
            'kategori' => 'pengumuman',
            'hari' => 80,
            'sematkan' => false,
            'ringkasan' => 'Kegiatan belajar diliburkan selama hari raya. Siswa diharapkan memanfaatkan waktu libur untuk berkumpul bersama keluarga.',
            'isi' => [
                ['p' => 'Sehubungan dengan peringatan Hari Raya Idul Adha 1447 H, kegiatan belajar mengajar di MTs NU Al Syairiyah diliburkan sesuai dengan ketetapan pemerintah.'],
                ['p' => 'Siswa diharapkan memanfaatkan waktu libur untuk berkumpul bersama keluarga dan mengikuti kegiatan ibadah di lingkungan masing-masing.'],
                ['p' => 'Kegiatan belajar akan kembali dimulai pada hari pertama masuk setelah libur. Jadwal kegiatan yang tertunda akan diinformasikan oleh wali kelas.'],
            ],
        ],
        [
            'judul' => 'Rencana Study Tour Semester Genap 2026/2027',
            'kategori' => 'kegiatan',
            'hari' => 5,
            'status' => 'draft',
            'sematkan' => false,
            'ringkasan' => 'Draft rencana kegiatan study tour semester genap yang masih dalam pembahasan panitia dan pimpinan madrasah.',
            'isi' => [
                ['p' => 'Madrasah tengah menyusun rencana kegiatan study tour untuk semester genap. Rencana ini masih dalam pembahasan panitia dan pimpinan madrasah.'],
                ['p' => 'Naskah ini belum final dan tidak untuk dipublikasikan. Detail tujuan, jadwal, serta pembiayaan akan diumumkan setelah mendapat persetujuan.'],
            ],
        ],
        [
            'judul' => 'Workshop Public Speaking bagi Pengurus OSIS',
            'kategori' => 'kegiatan',
            'hari' => 16,
            'status' => 'draft',
            'sematkan' => false,
            'ringkasan' => 'Draft rencana workshop public speaking untuk pengurus OSIS yang akan dilaksanakan pada semester genap.',
            'isi' => [
                ['p' => 'Madrasah berencana menggelar workshop public speaking bagi pengurus OSIS. Kegiatan ini ditujukan untuk melatih keberanian dan kemampuan menyampaikan gagasan di depan umum.'],
                ['p' => 'Rencana ini masih berupa draft dan menunggu penetapan jadwal serta narasumber.'],
            ],
        ],
    ];

    /**
     * Isi data artikel dummy.
     *
     * @var list<array<string, mixed>>
     */
    private const ARTIKEL = [
        [
            'judul' => 'Menumbuhkan Budaya Literasi di Madrasah',
            'hari' => 6,
            'sematkan' => true,
            'ringkasan' => 'Literasi bukan hanya soal membaca dan menulis, tetapi kemampuan memahami, menimbang, dan menyampaikan gagasan dengan jernih.',
            'isi' => [
                ['p' => 'Budaya literasi di madrasah tidak tumbuh dengan sendirinya. Ia lahir dari pembiasaan kecil yang dilakukan secara konsisten: membaca sebelum pelajaran dimulai, menulis catatan harian, hingga mendiskusikan buku di kelas.'],
                ['h2' => 'Mulai dari Kebiasaan Kecil'],
                ['p' => 'Guru dapat memulai dengan lima belas menit membaca setiap pagi. Kegiatan sederhana ini memberi ruang bagi siswa untuk mengenal beragam bacaan, dari cerita pendek hingga buku pengetahuan.'],
                ['p' => 'Setelah membaca, siswa diajak menuliskan satu paragraf refleksi. Tulisan tidak harus panjang. Yang penting adalah kebiasaan merumuskan pikiran sendiri dengan bahasa yang jernih.'],
                ['h2' => 'Ruang untuk Berbagi'],
                ['p' => 'Literasi akan lebih hidup apabila ada ruang untuk berbagi. Pojok baca, majalah dinding, dan forum presentasi kelas dapat menjadi tempat siswa menunjukkan hasil bacaannya.'],
                ['p' => 'Pada akhirnya, literasi adalah bekal untuk berpikir kritis. Siswa yang terbiasa membaca dan menulis akan lebih siap menimbang informasi, termasuk informasi yang datang dari media digital.'],
            ],
        ],
        [
            'judul' => 'Pendidikan Karakter di Era Digital',
            'hari' => 12,
            'sematkan' => false,
            'ringkasan' => 'Teknologi yang tumbuh cepat perlu diimbangi dengan pembinaan karakter agar siswa tetap santun dan bijak di ruang digital.',
            'isi' => [
                ['p' => 'Anak-anak hari ini tumbuh di tengah derasnya arus informasi digital. Mereka mengenal dunia melalui layar, jauh sebelum memahami cara menimbang isi yang mereka lihat.'],
                ['h2' => 'Bukan Sekadar Larangan'],
                ['p' => 'Pendidikan karakter tidak cukup dilakukan dengan melarang penggunaan gawai. Yang lebih penting adalah mendampingi siswa memahami batas, etika, dan tanggung jawab ketika berada di ruang digital.'],
                ['p' => 'Guru dan orang tua perlu membuka percakapan tentang jejak digital, keamanan data pribadi, serta dampak kata-kata yang ditulis di media sosial.'],
                ['h2' => 'Teladan dari Orang Dewasa'],
                ['p' => 'Siswa belajar dari apa yang mereka lihat. Ketika orang dewasa menunjukkan kebiasaan digital yang sehat, siswa akan menirunya tanpa perlu banyak nasihat.'],
                ['p' => 'Pendidikan karakter di era digital pada akhirnya adalah usaha bersama untuk menjaga akhlak di ruang yang tidak terlihat, tetapi dampaknya terasa nyata.'],
            ],
        ],
        [
            'judul' => 'Adab Sebelum Ilmu: Refleksi Tradisi Pesantren',
            'hari' => 20,
            'sematkan' => false,
            'ringkasan' => 'Tradisi pesantren mengajarkan bahwa ilmu yang bermanfaat selalu berjalan bersama adab kepada guru, kitab, dan sesama.',
            'isi' => [
                ['p' => 'Dalam tradisi pesantren, adab ditempatkan lebih dahulu daripada ilmu. Sebelum mempelajari isi kitab, seorang santri belajar bagaimana bersikap kepada guru, bagaimana memegang kitab, dan bagaimana duduk dalam majelis ilmu.'],
                ['p' => 'Tata krama itu bukan formalitas. Ia menjadi cara untuk menumbuhkan rasa hormat, kerendahan hati, dan kesiapan menerima ilmu dengan hati yang bersih.'],
                ['h2' => 'Relevansi di Madrasah'],
                ['p' => 'Nilai-nilai tersebut tetap relevan di madrasah hari ini. Siswa perlu memahami bahwa kecerdasan tanpa adab akan kehilangan arah, sedangkan adab akan menjaga ilmu tetap bermanfaat.'],
                ['p' => 'Guru pun dituntut menjadi teladan. Ketika guru memperlakukan siswa dengan santun, siswa belajar bahwa ilmu sejati selalu berjalan bersama akhlak mulia.'],
            ],
        ],
        [
            'judul' => 'Peran Orang Tua dalam Pendampingan Belajar',
            'hari' => 28,
            'sematkan' => false,
            'ringkasan' => 'Pendampingan orang tua di rumah menjadi kunci agar kebiasaan baik yang dibangun di madrasah dapat berlanjut.',
            'isi' => [
                ['p' => 'Keberhasilan belajar siswa tidak hanya ditentukan oleh waktu yang dihabiskan di kelas. Peran keluarga di rumah turut memberi warna pada kebiasaan belajar anak.'],
                ['p' => 'Pendampingan tidak selalu berarti menemani anak belajar setiap saat. Cukup dengan menyediakan waktu tenang untuk belajar, menanyakan kegiatan sekolah, dan memberi perhatian pada proses, bukan hanya hasil.'],
                ['h2' => 'Menjaga Komunikasi dengan Sekolah'],
                ['p' => 'Orang tua juga perlu menjaga komunikasi dengan wali kelas. Dengan begitu, persoalan belajar dapat diketahui lebih awal dan ditangani bersama-sama.'],
                ['p' => 'Anak yang merasa didampingi akan lebih percaya diri menghadapi tantangan belajar. Dukungan kecil yang konsisten sering kali lebih berarti daripada bantuan yang besar tetapi jarang.'],
            ],
        ],
        [
            'judul' => 'Mengelola Waktu Belajar yang Efektif',
            'hari' => 35,
            'sematkan' => false,
            'ringkasan' => 'Belajar lama belum tentu belajar efektif. Yang lebih penting adalah cara mengatur waktu, fokus, dan istirahat.',
            'isi' => [
                ['p' => 'Banyak siswa merasa sudah belajar lama, tetapi hasilnya belum sesuai harapan. Salah satu penyebabnya adalah cara mengelola waktu yang belum tepat.'],
                ['p' => 'Belajar dalam blok waktu pendek dengan fokus penuh biasanya lebih efektif daripada duduk berjam-jam sambil terdistraksi. Sisipkan istirahat singkat setiap 25 hingga 30 menit.'],
                ['h2' => 'Menyusun Prioritas'],
                ['p' => 'Mulailah dengan menyusun daftar tugas berdasarkan tenggat dan tingkat kesulitan. Kerjakan tugas yang paling menantang ketika energi masih penuh.'],
                ['p' => 'Jangan lupakan tidur yang cukup dan makan teratur. Tubuh yang bugar akan membantu otak bekerja lebih baik dalam memahami dan mengingat pelajaran.'],
            ],
        ],
        [
            'judul' => 'Pembelajaran Berdiferensiasi di Kelas VII',
            'hari' => 44,
            'sematkan' => false,
            'ringkasan' => 'Setiap siswa belajar dengan cara berbeda. Pembelajaran berdiferensiasi memberi ruang bagi keragaman kesiapan dan minat siswa.',
            'isi' => [
                ['p' => 'Di dalam satu kelas, kemampuan dan minat siswa tidak pernah seragam. Ada yang cepat memahami materi, ada pula yang perlu pendampingan lebih lama.'],
                ['p' => 'Pembelajaran berdiferensiasi mencoba menjawab keragaman tersebut dengan menyesuaikan isi, proses, atau produk belajar sesuai kebutuhan siswa.'],
                ['h2' => 'Bukan Berarti Berbeda Standar'],
                ['p' => 'Diferensiasi tidak berarti menurunkan standar. Semua siswa tetap diarahkan pada tujuan pembelajaran yang sama, hanya jalannya yang disesuaikan.'],
                ['p' => 'Guru dapat menyediakan bahan bacaan dengan tingkat kesulitan berbeda, memberi pilihan tugas presentasi atau tulisan, serta menyiapkan kelompok belajar dengan pendampingan yang sesuai.'],
                ['p' => 'Ketika siswa merasa belajarnya relevan dengan kemampuan dan minatnya, keterlibatan mereka di kelas cenderung meningkat.'],
            ],
        ],
        [
            'judul' => 'Menjaga Kesehatan Mental Remaja',
            'hari' => 52,
            'sematkan' => false,
            'ringkasan' => 'Remaja menghadapi tekanan belajar dan pergaulan yang tidak ringan. Dukungan emosional yang tepat membantu mereka tumbuh lebih sehat.',
            'isi' => [
                ['p' => 'Masa remaja adalah masa pencarian identitas. Di tengah tuntutan belajar dan pergaulan, tidak jarang siswa mengalami tekanan yang sulit diungkapkan.'],
                ['p' => 'Kesehatan mental bukan hal yang tabu untuk dibicarakan. Guru dan orang tua perlu peka terhadap perubahan perilaku, seperti menarik diri, mudah marah, atau kehilangan minat belajar.'],
                ['h2' => 'Mendengarkan Sebelum Menasihati'],
                ['p' => 'Langkah pertama yang paling berarti adalah mendengarkan. Beri ruang kepada siswa untuk bercerita tanpa langsung dihakimi atau dibandingkan dengan orang lain.'],
                ['p' => 'Madrasah dapat menyediakan layanan konseling dan pembinaan yang ramah. Siswa perlu tahu bahwa meminta bantuan bukan tanda kelemahan, melainkan bagian dari tanggung jawab menjaga diri.'],
            ],
        ],
        [
            'judul' => 'Kantin Sehat dan Jiwa Kewirausahaan Madrasah',
            'hari' => 60,
            'sematkan' => false,
            'ringkasan' => 'Kantin madrasah bisa menjadi laboratorium sederhana untuk belajar pola makan sehat, pengelolaan usaha, dan tanggung jawab.',
            'isi' => [
                ['p' => 'Kantin madrasah bukan sekadar tempat membeli makanan. Ia dapat menjadi ruang belajar tentang pola makan sehat, kebersihan, dan pengelolaan usaha kecil.'],
                ['p' => 'Dengan melibatkan siswa dalam pengelolaan kantin, mereka belajar menghitung harga, menjaga kualitas makanan, serta melayani pembeli dengan santun.'],
                ['h2' => 'Menanamkan Nilai Usaha yang Jujur'],
                ['p' => 'Kewirausahaan di madrasah sebaiknya dimulai dari nilai kejujuran dan tanggung jawab. Keuntungan bukan satu-satunya tujuan; yang terpenting adalah pengalaman belajar yang bermakna.'],
            ],
        ],
        [
            'judul' => 'Merawat Lingkungan sebagai Bagian dari Ibadah',
            'hari' => 68,
            'sematkan' => false,
            'ringkasan' => 'Menjaga kebersihan dan kelestarian lingkungan adalah bentuk syukur kepada Allah sekaligus tanggung jawab bersama warga madrasah.',
            'isi' => [
                ['p' => 'Islam mengajarkan bahwa manusia adalah khalifah di bumi. Menjaga lingkungan bukan hanya tugas pelajaran sains, tetapi bagian dari tanggung jawab seorang muslim.'],
                ['p' => 'Di madrasah, kepedulian lingkungan dapat dimulai dari hal sederhana: membuang sampah pada tempatnya, menghemat air, dan merawat tanaman di sekitar kelas.'],
                ['h2' => 'Pembiasaan yang Melibatkan Siswa'],
                ['p' => 'Kegiatan seperti jumat bersih, bank sampah, dan penanaman pohon dapat menjadi sarana pembiasaan yang melibatkan siswa secara langsung.'],
                ['p' => 'Ketika siswa merasakan hasilnya sendiri, kepedulian terhadap lingkungan tumbuh menjadi kesadaran, bukan sekadar kewajiban.'],
            ],
        ],
        [
            'judul' => 'Membangun Kebiasaan Shalat Berjamaah di Madrasah',
            'hari' => 74,
            'sematkan' => false,
            'ringkasan' => 'Pembiasaan shalat berjamaah di madrasah bukan hanya soal kewajiban, tetapi sarana melatih kedisiplinan dan kebersamaan siswa.',
            'isi' => [
                ['p' => 'Setiap hari, siswa MTs NU Al Syairiyah dibiasakan melaksanakan shalat berjamaah di musala madrasah. Kegiatan ini menjadi bagian dari rutinitas yang berjalan di sela jam pembelajaran.'],
                ['p' => 'Pembiasaan tersebut tidak hanya bertujuan memenuhi kewajiban ibadah. Melalui shalat berjamaah, siswa belajar disiplin waktu, merapikan barisan, dan saling mengingatkan dengan cara yang santun.'],
                ['h2' => 'Melatih Kedisiplinan dan Kebersamaan'],
                ['p' => 'Guru dan pengurus kelas mendampingi siswa agar terbiasa datang lebih awal, menjaga kebersihan tempat ibadah, dan mengikuti imam dengan tertib.'],
                ['p' => 'Harapannya, kebiasaan baik yang dibangun di madrasah dapat terbawa ke rumah dan lingkungan tempat tinggal siswa.'],
            ],
        ],
        [
            'judul' => 'Refleksi Semester: Apa yang Bisa Kita Perbaiki?',
            'hari' => 8,
            'status' => 'draft',
            'sematkan' => false,
            'ringkasan' => 'Draft catatan refleksi akhir semester untuk evaluasi pembelajaran dan pembinaan siswa.',
            'isi' => [
                ['p' => 'Refleksi akhir semester penting dilakukan untuk melihat apa yang sudah berjalan baik dan apa yang masih perlu diperbaiki dalam proses pembelajaran.'],
                ['p' => 'Naskah ini masih berupa draft dan akan dilengkapi setelah rapat evaluasi guru.'],
            ],
        ],
    ];

    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        $penulis = $this->penulis();

        $seed = 0;

        foreach (self::BERITA as $data) {
            $this->seedPost('berita', $data, $penulis, $seed++);
        }

        foreach (self::ARTIKEL as $data) {
            $this->seedPost('artikel', $data, $penulis, $seed++);
        }
    }

    /**
     * Simpan satu post beserta gambar sampul dummy.
     *
     * @param  array<string, mixed>  $data
     */
    private function seedPost(string $type, array $data, User $penulis, int $seed): void
    {
        $post = Post::updateOrCreate(
            ['judul' => $data['judul']],
            [
                'type' => $type,
                'ringkasan' => $data['ringkasan'],
                'isi' => $this->renderBlocks($data['isi']),
                'kategori' => $type === 'berita' ? $data['kategori'] : null,
                'status' => $data['status'] ?? 'publish',
                'sematkan' => $data['sematkan'] ?? false,
                'tanggal_publish' => now()->subDays($data['hari'])->toDateString(),
                'penulis_id' => $penulis->id,
            ],
        );

        $this->attachCover($post, $seed);
    }

    /**
     * Penulis default untuk seluruh post dummy.
     */
    private function penulis(): User
    {
        $penulis = User::query()
            ->whereIn('role', ['super_admin', 'admin'])
            ->orderBy('id')
            ->first();

        return $penulis ?? User::factory()->create([
            'name' => 'Redaksi MTs NU Al Syairiyah',
            'email' => 'redaksi@alsyairiyah.sch.id',
            'role' => 'admin',
            'aktif' => true,
        ]);
    }

    /**
     * Ubah struktur blok konten menjadi HTML.
     *
     * @param  list<array<string, mixed>>  $blocks
     */
    private function renderBlocks(array $blocks): string
    {
        $html = '';

        foreach ($blocks as $block) {
            if (isset($block['p'])) {
                $html .= '<p>'.$block['p'].'</p>';
            } elseif (isset($block['h2'])) {
                $html .= '<h2>'.$block['h2'].'</h2>';
            } elseif (isset($block['ul'])) {
                $html .= '<ul>';

                foreach ($block['ul'] as $item) {
                    $html .= '<li>'.$item.'</li>';
                }

                $html .= '</ul>';
            } elseif (isset($block['img'])) {
                $html .= '<img src="'.$block['img'].'" alt="'.($block['alt'] ?? '').'">';
            }
        }

        return $html;
    }

    /**
     * Lampirkan gambar sampul dummy bila post belum punya sampul.
     */
    private function attachCover(Post $post, int $seed): void
    {
        if ($post->hasMedia('sampul')) {
            return;
        }

        $path = $this->generateCover($seed);

        if ($path === null) {
            return;
        }

        try {
            $post->addMedia($path)
                ->usingFileName(Str::slug($post->judul).'.jpg')
                ->toMediaCollection('sampul');
        } finally {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * Buat gambar sampul abstrak agar layout punya variasi visual.
     */
    private function generateCover(int $seed): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $width = 1200;
        $height = 750;

        $image = imagecreatetruecolor($width, $height);

        if ($image === false) {
            return null;
        }

        $palette = self::COVER_PALETTES[$seed % count(self::COVER_PALETTES)];

        [$r1, $g1, $b1] = $palette['from'];
        [$r2, $g2, $b2] = $palette['to'];
        [$ar, $ag, $ab] = $palette['accent'];

        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $color = imagecolorallocate(
                $image,
                (int) round($r1 + ($r2 - $r1) * $ratio),
                (int) round($g1 + ($g2 - $g1) * $ratio),
                (int) round($b1 + ($b2 - $b1) * $ratio),
            );

            imagefilledrectangle($image, 0, $y, $width, $y, $color);
        }

        $accent = imagecolorallocatealpha($image, $ar, $ag, $ab, 86);
        $soft = imagecolorallocatealpha($image, 255, 255, 255, 116);
        $deep = imagecolorallocatealpha($image, 0, 0, 0, 104);

        $offset = ($seed * 137) % 260;

        imagefilledellipse($image, 900 + $offset, 120, 520, 520, $soft);
        imagefilledellipse($image, 1080 - $offset, 610, 300, 300, $deep);

        imagesetthickness($image, 8);
        imageellipse($image, 890 + $offset, 130, 620, 620, $accent);

        $band = imagecolorallocatealpha($image, 255, 255, 255, 122);
        imagefilledpolygon($image, [
            $width, 430,
            $width, 530,
            500, $height,
            340, $height,
        ], $band);

        $archLeft = 140 + ($seed * 43) % 180;
        $archWidth = 230;
        $archCenter = $archLeft + (int) ($archWidth / 2);

        $archLayer = imagecreatetruecolor($width, $height);
        imagealphablending($archLayer, false);
        imagesavealpha($archLayer, true);
        imagefill($archLayer, 0, 0, imagecolorallocatealpha($archLayer, 0, 0, 0, 127));

        $archColor = imagecolorallocate($archLayer, $ar, $ag, $ab);
        imagefilledrectangle($archLayer, $archLeft, 460, $archLeft + $archWidth, $height, $archColor);
        imagefilledellipse($archLayer, $archCenter, 460, $archWidth, $archWidth, $archColor);

        imagecopymerge($image, $archLayer, 0, 0, 0, 0, $width, $height, 46);
        imagedestroy($archLayer);

        $tempPath = tempnam(sys_get_temp_dir(), 'post-cover-');

        if ($tempPath === false) {
            imagedestroy($image);

            throw new RuntimeException('Gagal membuat berkas sementara untuk gambar sampul.');
        }

        imagejpeg($image, $tempPath, 88);
        imagedestroy($image);

        return $tempPath;
    }
}

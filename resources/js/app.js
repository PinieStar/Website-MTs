import Alpine from 'alpinejs';

// Livewire 3 sudah membawa Alpine sendiri. Jangan mulai instance kedua
// jika Livewire sudah memuatnya lebih dulu (mis. di halaman panel admin).
if (! window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}

import Alpine from 'alpinejs';
import { tiptapEditor } from './tiptap-editor';

// Daftarkan komponen Alpine khusus panel admin pada instance yang dipakai
// halaman ini. Livewire sudah membawa Alpine sendiri di panel admin.
function registerAdminComponents() {
    if (! window.Alpine || window.Alpine.__mtsAdminComponentsRegistered) {
        return;
    }

    window.Alpine.data('tiptapEditor', tiptapEditor);
    window.Alpine.__mtsAdminComponentsRegistered = true;
}

document.addEventListener('alpine:init', registerAdminComponents);
document.addEventListener('livewire:init', registerAdminComponents);
registerAdminComponents();

if (! window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}

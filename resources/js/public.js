import { initAnimations } from './public/animations';
import { initNavigation } from './public/navigation';
import { initInteractions } from './public/interactions';
import { setupTransitions } from './public/transitions';
import { setupTheme } from './public/theme';

setupTransitions();
setupTheme();

let booted = false;

function bootPage() {
    if (booted) {
        return;
    }

    try {
        initNavigation();
        initInteractions();
        initAnimations();
        document.documentElement.classList.add('js');
        window.__mtsAnimationsReady = true;
        booted = true;
    } catch (error) {
        document.documentElement.classList.remove('js');
        console.error('[MTs NU Al Syairiyah] Animasi gagal dimuat.', error);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        () => {
            window.setTimeout(bootPage, 80);
        },
        { once: true },
    );
} else {
    window.setTimeout(bootPage, 80);
}

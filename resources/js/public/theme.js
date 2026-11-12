const STORAGE_KEY = 'mts-theme';

let started = false;

function currentTheme() {
    return document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
}

function syncControls(theme) {
    const isDark = theme === 'dark';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(isDark));
        button.setAttribute('aria-label', isDark ? 'Ganti ke tema terang' : 'Ganti ke tema gelap');
        button.setAttribute('title', isDark ? 'Tema terang' : 'Tema gelap');
    });

    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) {
        meta.content = isDark ? '#061918' : '#042F2E';
    }
}

function applyTheme(theme, persist) {
    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;

    if (persist) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch {
            /* penyimpanan dinonaktifkan: tema tetap berlaku untuk sesi ini */
        }
    }

    syncControls(theme);
}

export function setupTheme() {
    if (started) {
        return;
    }

    started = true;

    document.addEventListener('click', (event) => {
        const target = event.target;
        const button = target instanceof Element ? target.closest('[data-theme-toggle]') : null;
        if (!button) {
            return;
        }

        applyTheme(currentTheme() === 'dark' ? 'light' : 'dark', true);
    });

    syncControls(currentTheme());
}

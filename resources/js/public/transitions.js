import { gsap } from 'gsap';

const TRANSITION_KEY = 'mts-transition';

let started = false;

function overlay() {
    return document.querySelector('[data-page-transition]');
}

function mark(root) {
    return root.querySelector('[data-transition-mark]');
}

function playEnter() {
    const layer = overlay();
    if (!layer) {
        return Promise.resolve();
    }

    const logo = mark(layer);
    gsap.killTweensOf([layer, logo].filter(Boolean));

    layer.dataset.active = 'true';
    gsap.set(layer, { visibility: 'visible', yPercent: 100 });
    if (logo) {
        gsap.set(logo, { opacity: 0, y: 12 });
    }

    return new Promise((resolve) => {
        const timeline = gsap.timeline({ onComplete: resolve });
        timeline.to(layer, { yPercent: 0, duration: 0.5, ease: 'power3.inOut' });
        if (logo) {
            timeline.to(logo, { opacity: 1, y: 0, duration: 0.3, ease: 'power2.out' }, '-=0.4');
        }
    });
}

function playExit(layer) {
    const logo = mark(layer);
    gsap.killTweensOf([layer, logo].filter(Boolean));

    document.documentElement.classList.remove('is-transitioning');

    gsap.set(layer, { visibility: 'visible', yPercent: 0 });
    if (logo) {
        gsap.set(logo, { opacity: 1, y: 0 });
    }

    gsap
        .timeline({
            onComplete: () => {
                layer.dataset.active = 'false';
                gsap.set(layer, { visibility: 'hidden', yPercent: 101 });
                if (logo) {
                    gsap.set(logo, { clearProps: 'opacity,transform' });
                }
            },
        })
        .to(layer, { yPercent: -101, duration: 0.6, ease: 'expo.inOut' });
}

function shouldTransition(event, anchor) {
    if (!anchor || event.defaultPrevented) {
        return false;
    }

    if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return false;
    }

    if (anchor.target && anchor.target !== '_self') {
        return false;
    }

    if (anchor.hasAttribute('download') || anchor.dataset.noTransition !== undefined) {
        return false;
    }

    const href = anchor.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
        return false;
    }

    let url;
    try {
        url = new URL(anchor.href, window.location.href);
    } catch {
        return false;
    }

    if (url.origin !== window.location.origin) {
        return false;
    }

    if (url.pathname === window.location.pathname && url.search === window.location.search) {
        return false;
    }

    return true;
}

export function setupTransitions() {
    if (started) {
        return;
    }

    started = true;

    const layer = overlay();
    if (!layer) {
        return;
    }

    if (sessionStorage.getItem(TRANSITION_KEY) === '1') {
        sessionStorage.removeItem(TRANSITION_KEY);
        layer.dataset.active = 'true';

        // Beri panel kesempatan tampil satu frame sebelum animasi keluar,
        // supaya transisi tidak terlewat saat halaman baru dimuat cepat.
        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => playExit(layer));
        });
    } else {
        gsap.set(layer, { visibility: 'hidden', yPercent: 101 });
    }

    document.addEventListener('click', (event) => {
        const target = event.target;
        const anchor = target instanceof Element ? target.closest('a[href]') : null;

        if (!shouldTransition(event, anchor)) {
            return;
        }

        event.preventDefault();

        try {
            sessionStorage.setItem(TRANSITION_KEY, '1');
        } catch {
            /* abaikan bila penyimpanan sesi tidak tersedia */
        }

        playEnter().finally(() => {
            window.location.href = anchor.href;
        });
    });

    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) {
            return;
        }

        const current = overlay();
        if (current) {
            current.dataset.active = 'false';
            gsap.set(current, { visibility: 'hidden', yPercent: 101 });
        }
    });
}

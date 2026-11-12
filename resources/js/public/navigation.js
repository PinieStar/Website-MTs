import { gsap } from 'gsap';

const DESKTOP = '(min-width: 1024px)';

const EASE = 'power3.out';
const EASE_INOUT = 'power3.inOut';

export function initNavigation() {
    const controller = new AbortController();
    const { signal } = controller;

    const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    const prefersReducedMotion = () => reduceMotionQuery.matches;

    const header = document.querySelector('[data-site-header]');
    const panel = document.querySelector('[data-mobile-panel]');
    const openButton = document.querySelector('[data-menu-open]');
    const closeButton = document.querySelector('[data-menu-close]');
    const links = Array.from(panel?.querySelectorAll('[data-mobile-link], [data-mobile-toggle]') ?? []);

    if (!header) {
        return () => controller.abort();
    }

    /* ------------------------ Isi navigasi desktop ------------------------ */
    const navList = header.querySelector('[data-nav-list]');
    const navLinks = Array.from(header.querySelectorAll('[data-nav-link]'));
    const activeNavLink = navLinks.find((link) => link.getAttribute('aria-current') === 'page') ?? null;

    function setNavFill(target) {
        const link = target ?? activeNavLink;
        navLinks.forEach((item) => {
            if (item === link) {
                item.dataset.pill = 'true';
            } else {
                delete item.dataset.pill;
            }
        });
    }

    navLinks.forEach((link) => {
        link.addEventListener('mouseenter', () => setNavFill(link), { signal });
        link.addEventListener('focus', () => setNavFill(link), { signal });
    });

    navList?.addEventListener('mouseleave', () => setNavFill(null), { signal });
    navList?.addEventListener(
        'focusout',
        (event) => {
            const next = event.relatedTarget;
            if (next instanceof Node && navList.contains(next)) {
                return;
            }
            setNavFill(null);
        },
        { signal },
    );

    setNavFill(null);

    let menuOpen = false;
    let menuTimeline = null;
    let lastFocused = null;
    let scrollFrame = 0;

    const focusables = () =>
        Array.from(panel?.querySelectorAll('a[href], button:not([disabled])') ?? []).filter(
            (element) => element.offsetParent !== null,
        );

    function openMenu() {
        if (!panel || !openButton || menuOpen) {
            return;
        }

        menuOpen = true;
        lastFocused = document.activeElement instanceof HTMLElement ? document.activeElement : null;
        panel.inert = false;
        panel.setAttribute('aria-hidden', 'false');
        panel.dataset.open = 'true';
        openButton.setAttribute('aria-expanded', 'true');
        document.body.classList.add('is-locked');

        const reduced = prefersReducedMotion();

        gsap.set(panel, {
            visibility: 'visible',
            clipPath: reduced ? 'inset(0 0 0% 0)' : 'inset(0 0 100% 0)',
            opacity: reduced ? 1 : 0.4,
            filter: reduced ? 'blur(0px)' : 'blur(6px)',
        });

        menuTimeline?.kill();
        menuTimeline = gsap.timeline();

        if (reduced) {
            gsap.set(links, { y: 0, opacity: 1, clearProps: 'transform' });
            window.setTimeout(() => closeButton?.focus(), 0);
            return;
        }

        menuTimeline
            .to(panel, {
                clipPath: 'inset(0 0 0% 0)',
                opacity: 1,
                filter: 'blur(0px)',
                duration: 0.6,
                ease: EASE_INOUT,
            })
            .fromTo(
                links,
                { y: 22, opacity: 0 },
                {
                    y: 0,
                    opacity: 1,
                    duration: 0.5,
                    stagger: 0.045,
                    ease: EASE,
                    clearProps: 'transform',
                },
                '-=0.3',
            );

        window.setTimeout(() => closeButton?.focus(), 140);
    }

    function closeMenu(restoreFocus = true) {
        if (!panel || !openButton || !menuOpen) {
            return;
        }

        menuOpen = false;
        openButton.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('is-locked');

        const reduced = prefersReducedMotion();

        menuTimeline?.kill();
        menuTimeline = gsap.timeline({
            onComplete: () => {
                if (!panel) {
                    return;
                }
                panel.dataset.open = 'false';
                panel.inert = true;
                gsap.set(panel, { clearProps: 'clipPath,visibility,opacity,filter' });
            },
        });

        if (reduced) {
            gsap.set(panel, { clipPath: 'inset(0 0 100% 0)' });
            if (restoreFocus) {
                lastFocused?.focus();
            }
            menuTimeline.eventCallback('onComplete', () => {
                panel.dataset.open = 'false';
                panel.inert = true;
                gsap.set(panel, { clearProps: 'clipPath,visibility,opacity,filter' });
            });
            return;
        }

        menuTimeline
            .to(links, {
                y: -14,
                opacity: 0,
                duration: 0.22,
                stagger: { each: 0.018, from: 'end' },
                ease: 'power2.in',
            })
            .to(
                panel,
                {
                    clipPath: 'inset(0 0 100% 0)',
                    opacity: 0.4,
                    filter: 'blur(6px)',
                    duration: 0.4,
                    ease: EASE_INOUT,
                },
                '-=0.08',
            );

        if (restoreFocus) {
            lastFocused?.focus();
        }
    }

    openButton?.addEventListener('click', openMenu, { signal });
    closeButton?.addEventListener('click', () => closeMenu(), { signal });

    panel?.querySelectorAll('[data-mobile-link]').forEach((link) => {
        link.addEventListener('click', () => closeMenu(false), { signal });
    });

    /* --------------------------- Mobile accordions --------------------------- */
    const mobileToggles = Array.from(panel?.querySelectorAll('[data-mobile-toggle]') ?? []);

    function animateSubmenu(submenu, open) {
        if (prefersReducedMotion()) {
            gsap.set(submenu, open ? { height: 'auto', opacity: 1 } : { height: 0, opacity: 0 });
            return;
        }

        gsap.killTweensOf(submenu);
        if (open) {
            gsap.set(submenu, { height: 'auto' });
            gsap.from(submenu, {
                height: 0,
                opacity: 0,
                duration: 0.4,
                ease: EASE,
            });
        } else {
            gsap.to(submenu, {
                height: 0,
                opacity: 0,
                duration: 0.3,
                ease: 'power2.in',
            });
        }
    }

    const setMobileSubmenu = (toggle, open) => {
        const targetId = toggle.getAttribute('aria-controls');
        const submenu = targetId ? panel?.querySelector(`#${targetId}`) : null;
        if (!submenu) {
            return;
        }

        toggle.setAttribute('aria-expanded', String(open));
        submenu.setAttribute('data-open', String(open));
        animateSubmenu(submenu, open);
    };

    mobileToggles.forEach((toggle) => {
        toggle.addEventListener(
            'click',
            () => {
                const willOpen = toggle.getAttribute('aria-expanded') !== 'true';
                mobileToggles.forEach((other) => {
                    if (other !== toggle) {
                        setMobileSubmenu(other, false);
                    }
                });
                setMobileSubmenu(toggle, willOpen);
            },
            { signal },
        );
    });

    document.addEventListener(
        'keydown',
        (event) => {
            if (event.key === 'Escape' && menuOpen) {
                closeMenu();
                return;
            }

            if (event.key !== 'Tab' || !menuOpen || !panel) {
                return;
            }

            const items = focusables();
            if (!items.length) {
                return;
            }
            const first = items[0];
            const last = items[items.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        },
        { signal },
    );

    /* ----------------------------- Dropdowns ----------------------------- */
    const dropdownItems = Array.from(header.querySelectorAll('[data-nav-item]')).filter((item) =>
        item.querySelector('[data-dropdown-panel]'),
    );

    function animateDropdown(dropdown, open) {
        gsap.killTweensOf(dropdown);

        if (prefersReducedMotion()) {
            gsap.set(dropdown, open ? { opacity: 1, y: 0 } : { opacity: 0, y: -6 });
            return;
        }

        if (open) {
            gsap.fromTo(
                dropdown,
                { opacity: 0, y: -8, filter: 'blur(4px)' },
                { opacity: 1, y: 0, filter: 'blur(0px)', duration: 0.32, ease: EASE },
            );
        } else {
            gsap.to(dropdown, { opacity: 0, y: -6, filter: 'blur(4px)', duration: 0.18, ease: 'power2.in' });
        }
    }

    dropdownItems.forEach((item) => {
        const trigger = item.querySelector('[data-dropdown-trigger]');
        const dropdown = item.querySelector('[data-dropdown-panel]');
        if (!trigger || !dropdown) {
            return;
        }

        const open = () => {
            dropdownItems.forEach((other) => {
                if (other === item) {
                    return;
                }
                const otherPanel = other.querySelector('[data-dropdown-panel]');
                if (otherPanel) {
                    otherPanel.setAttribute('data-open', 'false');
                    animateDropdown(otherPanel, false);
                }
                other.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
            });
            dropdown.setAttribute('data-open', 'true');
            trigger.setAttribute('aria-expanded', 'true');
            animateDropdown(dropdown, true);
        };

        const close = () => {
            dropdown.setAttribute('data-open', 'false');
            trigger.setAttribute('aria-expanded', 'false');
            animateDropdown(dropdown, false);
        };

        trigger.addEventListener(
            'click',
            (event) => {
                event.preventDefault();
                if (dropdown.getAttribute('data-open') === 'true') {
                    close();
                } else {
                    open();
                }
            },
            { signal },
        );

        item.addEventListener(
            'mouseenter',
            () => {
                if (window.matchMedia(DESKTOP).matches) {
                    open();
                }
            },
            { signal },
        );

        // Dropdown sengaja tidak ditutup saat kursor meninggalkan area.
        // Selama sudah terbuka lewat hover, ia bertahan sampai diklik,
        // Escape, atau klik di luar menu.

        dropdown.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => close(), { signal });
        });

        item.addEventListener(
            'focusout',
            (event) => {
                const next = event.relatedTarget;
                if (next instanceof Node && item.contains(next)) {
                    return;
                }
                close();
            },
            { signal },
        );
    });

    document.addEventListener(
        'keydown',
        (event) => {
            if (event.key !== 'Escape') {
                return;
            }
            dropdownItems.forEach((item) => {
                const dropdown = item.querySelector('[data-dropdown-panel]');
                if (dropdown) {
                    dropdown.setAttribute('data-open', 'false');
                    animateDropdown(dropdown, false);
                }
                item.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
            });
        },
        { signal },
    );

    document.addEventListener(
        'click',
        (event) => {
            const target = event.target;
            if (!(target instanceof Node)) {
                return;
            }
            if (header.contains(target) || panel?.contains(target)) {
                return;
            }

            dropdownItems.forEach((item) => {
                const dropdown = item.querySelector('[data-dropdown-panel]');
                if (dropdown) {
                    dropdown.setAttribute('data-open', 'false');
                    animateDropdown(dropdown, false);
                }
                item.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
            });
        },
        { signal },
    );

    /* ---------------------------- Smart navbar ---------------------------- */
    const COMPACT_AFTER = 50;
    let compact = header.classList.contains('is-scrolled');

    const syncHeaderState = () => {
        const next = window.scrollY > COMPACT_AFTER;
        if (next === compact) {
            return;
        }
        compact = next;
        header.classList.toggle('is-scrolled', compact);
    };

    const onScroll = () => {
        if (scrollFrame) {
            return;
        }
        scrollFrame = window.requestAnimationFrame(() => {
            scrollFrame = 0;
            if (signal.aborted) {
                return;
            }
            syncHeaderState();
        });
    };

    syncHeaderState();

    window.addEventListener('scroll', onScroll, { passive: true, signal });
    window.addEventListener(
        'resize',
        () => {
            if (window.matchMedia(DESKTOP).matches && menuOpen) {
                closeMenu(false);
            }
        },
        { signal },
    );

    return () => {
        controller.abort();
        window.cancelAnimationFrame(scrollFrame);
        menuTimeline?.kill();
        document.body.classList.remove('is-locked');

        if (panel) {
            panel.dataset.open = 'false';
            panel.setAttribute('aria-hidden', 'true');
            panel.inert = true;
            gsap.set(panel, { clearProps: 'clipPath,visibility,opacity,filter' });
        }

        dropdownItems.forEach((item) => {
            const dropdown = item.querySelector('[data-dropdown-panel]');
            if (dropdown) {
                gsap.killTweensOf(dropdown);
                dropdown.setAttribute('data-open', 'false');
            }
            item.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
        });
    };
}

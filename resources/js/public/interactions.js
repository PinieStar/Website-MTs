import { gsap } from 'gsap';

function initPager(signal) {
    document.querySelectorAll('[data-pager-grid]').forEach((grid) => {
        const name = grid.dataset.pagerGrid;
        if (!name) {
            return;
        }

        const nav = document.querySelector(`[data-pager="${name}"]`);
        if (!nav) {
            return;
        }

        const size = Number(grid.dataset.pageSize) || 12;
        const cards = Array.from(grid.querySelectorAll(`[data-filter-target="${name}"]`));
        if (!cards.length) {
            return;
        }

        let filter = 'semua';
        let page = 1;
        let busy = false;

        const stopMotion = (targets) => {
            if (!targets.length) {
                return;
            }
            gsap.killTweensOf(targets);
            gsap.set(targets, { clearProps: 'transform,translate,rotate,scale' });
        };

        const makeButton = (label, target, opts = {}) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pill tabular-nums';
            btn.textContent = label;
            btn.disabled = !!opts.disabled;
            btn.setAttribute('aria-pressed', String(!!opts.current));
            if (opts.current) {
                btn.setAttribute('aria-current', 'page');
            }
            if (opts.disabled) {
                btn.classList.add('opacity-40', 'pointer-events-none');
            }
            btn.addEventListener('click', () => {
                if (busy || target === page) {
                    return;
                }
                const direction = target > page ? 1 : -1;
                page = target;
                render({ scroll: true, animate: true, direction });
            });
            return btn;
        };

        const render = ({ scroll = false, animate = false, direction = 1 } = {}) => {
            const matched = cards.filter((card) => filter === 'semua' || card.dataset.category === filter);
            const pages = Math.max(1, Math.ceil(matched.length / size));
            page = Math.min(Math.max(page, 1), pages);

            const visible = new Set(matched.slice((page - 1) * size, page * size));
            const incoming = cards.filter((card) => visible.has(card));
            const outgoing = cards.filter(
                (card) => !visible.has(card) && !card.hasAttribute('data-page-hidden'),
            );

            const swap = () => {
                cards.forEach((card) => card.toggleAttribute('data-page-hidden', !visible.has(card)));

                nav.replaceChildren();
                nav.hidden = pages <= 1;
                if (pages > 1) {
                    nav.append(makeButton('‹ Sebelumnya', page - 1, { disabled: page === 1 }));
                    for (let i = 1; i <= pages; i++) {
                        nav.append(makeButton(String(i), i, { current: i === page }));
                    }
                    nav.append(makeButton('Berikutnya ›', page + 1, { disabled: page === pages }));
                }

                if (scroll) {
                    grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                window.dispatchEvent(new Event('resize'));

                if (animate) {
                    stopMotion(incoming);
                    if (incoming.length) {
                        gsap.fromTo(
                            incoming,
                            { opacity: 0, x: 28 * direction, scale: 0.96 },
                            {
                                opacity: 1,
                                x: 0,
                                y: 0,
                                scale: 1,
                                duration: 0.55,
                                ease: 'power3.out',
                                stagger: 0.05,
                                overwrite: true,
                                onComplete: () =>
                                    gsap.set(incoming, { clearProps: 'transform,translate,rotate,scale' }),
                            },
                        );
                    }

                    gsap.fromTo(
                        nav.children,
                        { opacity: 0, y: 6 },
                        { opacity: 1, y: 0, duration: 0.35, ease: 'power2.out', stagger: 0.02, overwrite: true },
                    );
                }

                busy = false;
            };

            stopMotion(outgoing);

            if (animate && outgoing.length) {
                busy = true;
                gsap.to(outgoing, {
                    opacity: 0,
                    x: -22 * direction,
                    duration: 0.22,
                    ease: 'power2.in',
                    stagger: 0.02,
                    overwrite: true,
                    onComplete: swap,
                });
            } else {
                swap();
            }
        };

        document.querySelectorAll(`[data-filter-group="${name}"] [data-filter]`).forEach((btn) => {
            btn.addEventListener(
                'click',
                () => {
                    filter = btn.dataset.filter ?? 'semua';
                    page = 1;
                    render({ animate: true });
                },
                { signal },
            );
        });

        render();
    });
}

function parseLightboxItems(element) {
    const raw = element.dataset.lightboxItems;
    if (!raw) {
        const src = (element.dataset.lightboxSrc ?? '').trim();
        if (!src) {
            return [];
        }
        return [
            {
                src,
                caption: element.dataset.lightboxCaption ?? '',
                alt: element.dataset.lightboxAlt ?? element.dataset.lightboxCaption ?? 'Foto galeri madrasah',
            },
        ];
    }

    try {
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed)) {
            return [];
        }
        return parsed
            .map((entry) => ({
                src: typeof entry?.src === 'string' ? entry.src.trim() : '',
                caption: typeof entry?.caption === 'string' ? entry.caption : '',
                alt: typeof entry?.alt === 'string' ? entry.alt : 'Foto galeri madrasah',
            }))
            .filter((entry) => entry.src.length > 0);
    } catch {
        return [];
    }
}

function initLightbox(signal) {
    const lightbox = document.querySelector('[data-lightbox]');
    if (!lightbox) {
        return;
    }

    const image = lightbox.querySelector('[data-lightbox-image]');
    const frame = lightbox.querySelector('[data-lightbox-frame]');
    const caption = lightbox.querySelector('[data-lightbox-caption]');
    const counter = lightbox.querySelector('[data-lightbox-counter]');
    const close = lightbox.querySelector('[data-lightbox-close]');
    const prev = lightbox.querySelector('[data-lightbox-prev]');
    const next = lightbox.querySelector('[data-lightbox-next]');

    const triggers = Array.from(document.querySelectorAll('[data-lightbox-items], [data-lightbox-src]'));
    if (!triggers.length) {
        return;
    }

    let items = [];
    let index = 0;
    let trigger = null;

    const updateNavVisibility = () => {
        const multi = items.length > 1;
        if (prev) {
            prev.hidden = !multi;
        }
        if (next) {
            next.hidden = !multi;
        }
        if (counter) {
            counter.hidden = !multi;
        }
    };

    const render = () => {
        const item = items[index];
        if (!item || !image) {
            return;
        }
        image.src = item.src;
        image.alt = item.alt;
        if (caption) {
            caption.textContent = item.caption;
        }
        if (counter) {
            counter.textContent = `${index + 1} / ${items.length}`;
        }
    };

    const step = (direction) => {
        if (items.length < 2) {
            return;
        }
        index = (index + direction + items.length) % items.length;
        render();
        gsap.fromTo(
            image,
            { opacity: 0.3, scale: 0.99 },
            { opacity: 1, scale: 1, duration: 0.35, ease: 'power2.out' },
        );
    };

    const open = (element, itemIndex) => {
        const groupItems = parseLightboxItems(element);
        if (!groupItems.length) {
            return;
        }
        trigger = element;
        items = groupItems;
        index = itemIndex;
        updateNavVisibility();
        render();
        lightbox.showModal();
        document.body.classList.add('is-locked');
        close?.focus();
    };

    triggers.forEach((element) => {
        element.addEventListener('click', () => open(element, 0), { signal });
    });

    close?.addEventListener('click', () => lightbox.close(), { signal });
    prev?.addEventListener('click', () => step(-1), { signal });
    next?.addEventListener('click', () => step(1), { signal });

    lightbox.addEventListener(
        'keydown',
        (event) => {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                step(-1);
            }
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                step(1);
            }
        },
        { signal },
    );

    lightbox.addEventListener(
        'close',
        () => {
            document.body.classList.remove('is-locked');
            if (image) {
                image.removeAttribute('src');
            }
            trigger?.focus();
        },
        { signal },
    );

    lightbox.addEventListener(
        'click',
        (event) => {
            if (event.target === lightbox) {
                lightbox.close();
            }
        },
        { signal },
    );

    if (frame) {
        let startX = 0;
        let startY = 0;
        let dragging = false;
        let moved = false;
        const threshold = 40;

        const onStart = (clientX, clientY) => {
            if (items.length < 2) {
                return;
            }
            startX = clientX;
            startY = clientY;
            dragging = true;
            moved = false;
        };

        const onMove = (clientX, clientY) => {
            if (!dragging) {
                return;
            }
            const dx = clientX - startX;
            const dy = clientY - startY;
            if (Math.abs(dx) > 8 || Math.abs(dy) > 8) {
                moved = true;
            }
            if (Math.abs(dx) > Math.abs(dy) && image) {
                image.style.transform = `translateX(${dx * 0.4}px)`;
            }
        };

        const onEnd = (clientX) => {
            if (!dragging) {
                return;
            }
            dragging = false;
            const dx = clientX - startX;
            if (image) {
                image.style.transform = '';
            }
            if (!moved) {
                return;
            }
            if (dx > threshold) {
                step(-1);
            } else if (dx < -threshold) {
                step(1);
            }
        };

        frame.addEventListener(
            'touchstart',
            (event) => {
                const touch = event.touches[0];
                if (touch) {
                    onStart(touch.clientX, touch.clientY);
                }
            },
            { signal, passive: true },
        );
        frame.addEventListener(
            'touchmove',
            (event) => {
                const touch = event.touches[0];
                if (touch) {
                    onMove(touch.clientX, touch.clientY);
                }
            },
            { signal, passive: true },
        );
        frame.addEventListener(
            'touchend',
            (event) => {
                const touch = event.changedTouches[0];
                if (touch) {
                    onEnd(touch.clientX);
                }
            },
            { signal },
        );

        frame.addEventListener(
            'pointerdown',
            (event) => {
                if (event.pointerType === 'touch') {
                    return;
                }
                onStart(event.clientX, event.clientY);
            },
            { signal },
        );
        frame.addEventListener(
            'pointermove',
            (event) => {
                if (event.pointerType === 'touch') {
                    return;
                }
                onMove(event.clientX, event.clientY);
            },
            { signal },
        );
        frame.addEventListener(
            'pointerup',
            (event) => {
                if (event.pointerType === 'touch') {
                    return;
                }
                onEnd(event.clientX);
            },
            { signal },
        );
    }
}

function initFadeImages() {
    document.querySelectorAll('[data-fade-img]').forEach((img) => {
        const show = () => img.classList.remove('opacity-0');
        if (img.complete) {
            show();
        } else {
            img.addEventListener('load', show, { once: true });
            img.addEventListener('error', show, { once: true });
        }
    });
}

export function initInteractions() {
    const controller = new AbortController();
    const { signal } = controller;

    initPager(signal);
    initLightbox(signal);
    initFadeImages();

    return () => {
        controller.abort();
        document.body.classList.remove('is-locked');
        document.querySelectorAll('dialog[open]').forEach((dialog) => dialog.close());
    };
}

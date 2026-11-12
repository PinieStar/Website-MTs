import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

const EASE = 'power3.out';

function initLineReveals() {
    document.querySelectorAll('[data-reveal="lines"]').forEach((element) => {
        SplitText.create(element, {
            type: 'lines',
            mask: 'lines',
            linesClass: 'is-split-line',
            autoSplit: true,
            onSplit(self) {
                gsap.set(element, { opacity: 1 });
                gsap.set(self.lines, { yPercent: 115, opacity: 0 });

                return gsap.to(self.lines, {
                    yPercent: 0,
                    opacity: 1,
                    duration: 0.9,
                    stagger: 0.08,
                    ease: EASE,
                    scrollTrigger: {
                        trigger: element,
                        start: 'top 88%',
                        once: true,
                    },
                });
            },
        });
    });
}

function initFadeReveals() {
    document.querySelectorAll('[data-reveal="fade"]').forEach((element) => {
        if (element.closest('[data-stagger]')) {
            gsap.set(element, { opacity: 1 });
            return;
        }

        gsap.fromTo(
            element,
            { y: 24, opacity: 0 },
            {
                y: 0,
                opacity: 1,
                duration: 0.9,
                ease: EASE,
                scrollTrigger: { trigger: element, start: 'top 91%', once: true },
                onComplete: () => gsap.set(element, { clearProps: 'transform,translate,rotate,scale' }),
            },
        );
    });
}

function initStagger() {
    document.querySelectorAll('[data-stagger]').forEach((group) => {
        const children = Array.from(group.children).filter((child) => child instanceof HTMLElement);
        if (!children.length) {
            return;
        }

        gsap.fromTo(
            children,
            { y: 30, opacity: 0, scale: 0.975 },
            {
                y: 0,
                opacity: 1,
                scale: 1,
                duration: 0.85,
                ease: EASE,
                stagger: 0.085,
                scrollTrigger: { trigger: group, start: 'top 88%', once: true },
                onComplete: () => gsap.set(children, { clearProps: 'transform,translate,rotate,scale' }),
            },
        );
    });
}

function initGoldRules() {
    document.querySelectorAll('[data-gold-line]').forEach((rule) => {
        gsap.fromTo(
            rule,
            { scaleX: 0 },
            {
                scaleX: 1,
                duration: 0.8,
                ease: EASE,
                scrollTrigger: { trigger: rule, start: 'top 95%', once: true },
            },
        );
    });
}

function initArchReveals() {
    document.querySelectorAll('[data-arch]').forEach((element) => {
        gsap.fromTo(
            element,
            { clipPath: 'inset(100% 0% 0% 0%)' },
            {
                clipPath: 'inset(0% 0% 0% 0%)',
                duration: 1.25,
                ease: 'expo.out',
                scrollTrigger: { trigger: element, start: 'top 88%', once: true },
            },
        );
    });
}

function initCounters() {
    document.querySelectorAll('[data-count]').forEach((element) => {
        const target = Number(element.dataset.count);
        if (!Number.isFinite(target)) {
            return;
        }

        const suffix = element.dataset.countSuffix ?? '';
        const formatter = new Intl.NumberFormat('id-ID');
        const counter = { value: 0 };
        element.textContent = `${formatter.format(0)}${suffix}`;

        gsap.to(counter, {
            value: target,
            duration: 1.7,
            ease: 'power2.out',
            snap: { value: 1 },
            scrollTrigger: { trigger: element, start: 'top 92%', once: true },
            onUpdate() {
                element.textContent = `${formatter.format(counter.value)}${suffix}`;
            },
        });
    });
}

function initParallax() {
    const media = gsap.matchMedia();
    media.add('(min-width: 768px)', () => {
        document.querySelectorAll('[data-parallax]').forEach((element) => {
            const strength = Number(element.dataset.parallax ?? 0.12);
            const frame = element.closest('[data-parallax-frame]') ?? element;

            gsap.fromTo(
                element,
                { yPercent: -strength * 100 },
                {
                    yPercent: strength * 100,
                    ease: 'none',
                    scrollTrigger: {
                        trigger: frame,
                        start: 'top bottom',
                        end: 'bottom top',
                        scrub: true,
                    },
                },
            );
        });
    });
}

function initMagnetic() {
    const media = gsap.matchMedia();
    media.add('(hover: hover) and (pointer: fine) and (min-width: 1024px)', () => {
        document.querySelectorAll('[data-magnetic]').forEach((element) => {
            const xTo = gsap.quickTo(element, 'x', { duration: 0.45, ease: 'power3.out' });
            const yTo = gsap.quickTo(element, 'y', { duration: 0.45, ease: 'power3.out' });
            const move = (event) => {
                const bounds = element.getBoundingClientRect();
                xTo((event.clientX - bounds.left - bounds.width / 2) * 0.18);
                yTo((event.clientY - bounds.top - bounds.height / 2) * 0.28);
            };
            const reset = () => {
                xTo(0);
                yTo(0);
            };

            element.addEventListener('mousemove', move);
            element.addEventListener('mouseleave', reset);

            return () => {
                element.removeEventListener('mousemove', move);
                element.removeEventListener('mouseleave', reset);
                gsap.set(element, { x: 0, y: 0 });
            };
        });
    });
}

export function initAnimations() {
    gsap.context(() => {
        initLineReveals();
        initFadeReveals();
        initStagger();
        initGoldRules();
        initArchReveals();
        initCounters();
        initParallax();
        initMagnetic();
    }, document.body);

    if ('fonts' in document) {
        document.fonts.ready.then(() => ScrollTrigger.refresh());
    }
}

/*
 * Apparition des éléments au défilement, et compteurs qui défilent jusqu'à leur valeur.
 */
export function initReveal(root = document) {
    const items = [...root.querySelectorAll('[data-reveal]:not(.is-visible)')];
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        // Les éléments qui entrent ensemble apparaissent l'un après l'autre.
        entries.filter((e) => e.isIntersecting).forEach((entry, i) => {
            const el = entry.target;
            el.style.setProperty('--delay', `${Math.min(i, 6) * 70}ms`);
            el.classList.add('is-visible');
            el.querySelectorAll('[data-count]').forEach(countUp);
            observer.unobserve(el);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    items.forEach((el) => observer.observe(el));
}

function countUp(el) {
    const target = parseInt(el.dataset.count, 10);
    if (!Number.isFinite(target) || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const duration = 900 + target * 25;
    const start = performance.now();

    function frame(now) {
        const p = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = String(Math.round(target * eased));
        if (p < 1) requestAnimationFrame(frame);
    }
    el.textContent = '0';
    requestAnimationFrame(frame);
}

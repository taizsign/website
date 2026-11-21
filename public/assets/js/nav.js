/*
 * Barre de navigation : fond flouté une fois la page défilée,
 * masquée quand on descend, rappelée dès qu'on remonte. Menu plein écran sur mobile.
 */
export function initNav() {
    const bar = document.querySelector('[data-bar]');
    if (!bar) return;

    let lastY = scrollY, ticking = false;

    function update() {
        ticking = false;
        const y = scrollY;
        bar.classList.toggle('is-scrolled', y > 24);
        if (!document.body.classList.contains('menu-open')) {
            bar.classList.toggle('is-hidden', y > lastY && y > 320);
        }
        lastY = y;
    }

    addEventListener('scroll', () => {
        if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }, { passive: true });
    update();

    // Menu mobile
    const burger = bar.querySelector('[data-burger]');
    const menu = document.querySelector('[data-menu]');
    if (!burger || !menu) return;

    function setOpen(open) {
        burger.setAttribute('aria-expanded', String(open));
        menu.hidden = !open;
        document.body.classList.toggle('menu-open', open);
        document.body.style.overflow = open ? 'hidden' : '';
        if (open) menu.querySelector('a')?.focus();
    }

    burger.addEventListener('click', () => setOpen(burger.getAttribute('aria-expanded') !== 'true'));
    menu.addEventListener('click', (e) => { if (e.target.closest('a')) setOpen(false); });
    addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !menu.hidden) { setOpen(false); burger.focus(); }
    });
}

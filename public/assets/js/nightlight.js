/*
 * Filtre de lumière bleue : l'icône du logo est un interrupteur.
 * Une teinte chaude (fusion « multiply ») recouvre tout le site en se propageant
 * en cercle depuis le bouton, et se rétracte vers lui quand on l'éteint.
 * Le choix est mémorisé ; boot.js le réapplique avant l'affichage des pages suivantes.
 */
export function initNightlight() {
    const button = document.querySelector('[data-nightlight]');
    const layer = document.querySelector('[data-nightlight-layer]');
    if (!button || !layer) return;

    const root = document.documentElement;
    const tip = button.querySelector('[data-nightlight-tip]');

    let stored = null;
    try { stored = localStorage.getItem('nightlight'); } catch { /* stockage indisponible */ }

    // Le cercle part du centre du bouton.
    function anchor() {
        const r = button.getBoundingClientRect();
        layer.style.setProperty('--nx', `${(r.left + r.width / 2).toFixed(1)}px`);
        layer.style.setProperty('--ny', `${(r.top + r.height / 2).toFixed(1)}px`);
    }

    // Les textes viennent du HTML (data-label-on, data-tip-off…), donc dans la langue de la page.
    const text = button.dataset;

    function sync() {
        const on = root.classList.contains('night');
        button.setAttribute('aria-pressed', String(on));
        button.setAttribute('aria-label', on ? text.labelOn : text.labelOff);
        tip.textContent = on ? text.tipOn : text.tipOff;
    }

    anchor();
    sync();

    // Tant que le filtre n'a jamais été essayé, l'icône signale discrètement qu'elle est cliquable.
    if (stored === null) button.classList.add('is-hinting');

    // À chaque chargement, l'icône « démarre » pour attirer l'œil.
    // Si la page arrive par une transition, on attend qu'elle soit découverte.
    if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const delay = root.classList.contains('is-revealing') ? 300 : 0;
        setTimeout(() => {
            button.classList.add('is-booting');
            setTimeout(() => button.classList.remove('is-booting'), 1900);
        }, delay);
    }

    button.addEventListener('click', () => {
        anchor();
        const on = !root.classList.contains('night');
        root.classList.toggle('night', on);
        try { localStorage.setItem('nightlight', on ? '1' : '0'); } catch { /* tant pis */ }
        button.classList.remove('is-hinting');
        button.classList.add('is-switching');
        setTimeout(() => button.classList.remove('is-switching'), 500);
        sync();
    });

    addEventListener('resize', anchor);
}

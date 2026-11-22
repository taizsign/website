/*
 * Quand on ouvre une rubrique du menu (Projets, Compétences, Parcours, Veille, Labo),
 * une nuée de papillons (le même dessin que celui du titre, en plusieurs couleurs)
 * décolle du bas de la page, à gauche, et s'échappe par le haut ou par la droite,
 * à vitesse constante. Le tout dure 3 secondes, puis tout est retiré de la page.
 *
 * Pas sur la page d'accueil, pas sur le détail d'un projet, pas en revenant en arrière,
 * et une seule fois par rubrique et par visite pour ne pas lasser.
 */

const DURATION = 3000;
const SVG_NS = 'http://www.w3.org/2000/svg';

// [aile du haut, aile du bas]
const COLORS = [
    ['#6a2ee0', '#d9cdfb'], // violet, comme celui du titre
    ['#ff7a1a', '#ffd2a8'], // orange
    ['#12b5a5', '#b8f0e8'], // turquoise
    ['#e83e8c', '#ffc4dd'], // rose
    ['#f5b700', '#fff0b3'], // jaune
    ['#2f6fed', '#c7d8ff'], // bleu
    ['#3fae2a', '#cdeec4'], // vert
];

const WING = `
    <path d="M-1 -2 C -6 -18, -21 -19, -20 -9 C -19 -2, -9 1, -1 0 Z" class="wing__top"/>
    <path d="M-1 0 C -8 1, -17 5, -14 12 C -11 16, -4 9, -1 2 Z" class="wing__bottom"/>
    <circle cx="-13" cy="-9" r="2.2" class="wing__spot"/>`;

const MARKUP = `
    <g class="wing">${WING}</g>
    <g transform="scale(-1 1)"><g class="wing">${WING}</g></g>
    <g class="butterfly__body">
        <ellipse cx="0" cy="1" rx="1.6" ry="8"/>
        <path d="M-0.5 -6 C -2 -11, -4 -14, -6 -15 M0.5 -6 C 2 -11, 4 -14, 6 -15"/>
    </g>`;

const rand = (min, max) => min + Math.random() * (max - min);

function isSectionOpening() {
    const nav = performance.getEntriesByType?.('navigation')[0];
    if (nav?.type === 'back_forward') return false;

    // la page doit être une rubrique du menu principal
    const here = location.pathname.replace(/\/+$/, '');
    const isSection = [...document.querySelectorAll('.nav a')]
        .some((a) => new URL(a.href, location.href).pathname.replace(/\/+$/, '') === here);
    if (!isSection) return false;

    try {
        const seen = JSON.parse(sessionStorage.getItem('flock') || '[]');
        if (seen.includes(here)) return false;
        sessionStorage.setItem('flock', JSON.stringify([...seen, here]));
    } catch { /* stockage indisponible : on les montre quand même */ }
    return true;
}

/** Point d'une courbe de Bézier cubique, et sa tangente. */
function bezier(p, t) {
    const u = 1 - t;
    const point = [0, 1].map((i) =>
        u * u * u * p[0][i] + 3 * u * u * t * p[1][i] + 3 * u * t * t * p[2][i] + t * t * t * p[3][i]);
    const tangent = [0, 1].map((i) =>
        3 * u * u * (p[1][i] - p[0][i]) + 6 * u * t * (p[2][i] - p[1][i]) + 3 * t * t * (p[3][i] - p[2][i]));
    return [point, tangent];
}

/**
 * Le paramètre t d'une Bézier n'avance pas à vitesse constante le long de la courbe.
 * On mesure la longueur cumulée sur 120 points, puis on convertit « fraction de distance »
 * en t : le papillon parcourt la même distance à chaque image.
 */
function arcTable(curve, steps = 120) {
    const lengths = [0];
    let [prev] = bezier(curve, 0);
    for (let i = 1; i <= steps; i++) {
        const [pt] = bezier(curve, i / steps);
        lengths.push(lengths[i - 1] + Math.hypot(pt[0] - prev[0], pt[1] - prev[1]));
        prev = pt;
    }
    return lengths;
}

function paramAtDistance(lengths, fraction) {
    const target = fraction * lengths[lengths.length - 1];
    let lo = 0, hi = lengths.length - 1;
    while (lo < hi - 1) {
        const mid = (lo + hi) >> 1;
        if (lengths[mid] < target) lo = mid; else hi = mid;
    }
    const span = lengths[hi] - lengths[lo] || 1;
    return (lo + (target - lengths[lo]) / span) / (lengths.length - 1);
}

function makeButterfly(index, W, H) {
    const [top, bottom] = COLORS[index % COLORS.length];
    const size = rand(28, 46) * (W < 640 ? 0.8 : 1);

    const svg = document.createElementNS(SVG_NS, 'svg');
    svg.setAttribute('viewBox', '-22 -20 44 36');
    svg.setAttribute('class', 'flyer');
    svg.style.width = `${size}px`;
    svg.style.setProperty('--c1', top);
    svg.style.setProperty('--c2', bottom);
    svg.style.setProperty('--flap', `${rand(0.2, 0.26).toFixed(3)}s`);
    svg.style.setProperty('--phase', `${-rand(0, 0.26).toFixed(3)}s`);
    svg.innerHTML = MARKUP;

    // Départ : en bas de la page, côté gauche.
    const start = [W * rand(0.03, 0.2), H * rand(0.6, 0.88)];

    // Sortie : par le haut (le plus souvent) ou par la droite, toujours hors de l'écran.
    const end = Math.random() < 0.6
        ? [W * rand(0.35, 1.0), -size * 2 - rand(0, 60)]
        : [W + size * 2 + rand(0, 60), H * rand(-0.05, 0.4)];

    // Trajectoire presque droite : les points de contrôle restent sur la ligne
    // départ → sortie, à peine décalés sur le côté pour une courbe très douce.
    const dx = end[0] - start[0], dy = end[1] - start[1];
    const len = Math.hypot(dx, dy);
    const nx = -dy / len, ny = dx / len;
    const bend = len * rand(-0.07, 0.07);
    const curve = [
        start,
        [start[0] + dx / 3 + nx * bend, start[1] + dy / 3 + ny * bend],
        [start[0] + (2 * dx) / 3 + nx * bend, start[1] + (2 * dy) / 3 + ny * bend],
        end,
    ];

    const delay = rand(0, 550);

    return {
        svg,
        size,
        curve,
        lengths: arcTable(curve),
        delay,
        // chaque papillon finit sa course avant la fin des 3 secondes
        duration: DURATION - delay - rand(0, 350),
        sway: { amp: rand(3, 6), phase: rand(0, Math.PI * 2) },
        scale: rand(0.85, 1.15),
    };
}

export function initFlight() {
    if (matchMedia('(prefers-reduced-motion: reduce)').matches || !isSectionOpening()) return;

    const W = innerWidth, H = innerHeight;
    const count = W < 640 ? 6 : 10;

    const layer = document.createElement('div');
    layer.className = 'flock';
    layer.setAttribute('aria-hidden', 'true');

    const flock = Array.from({ length: count }, (_, i) => makeButterfly(i, W, H));
    flock.forEach((b) => layer.appendChild(b.svg));
    document.body.appendChild(layer);

    const t0 = performance.now();

    function frame(now) {
        const elapsed = now - t0;

        for (const b of flock) {
            const local = (elapsed - b.delay) / b.duration;
            if (local <= 0) continue;

            // vitesse constante le long de la courbe
            const t = Math.min(1, local);
            const [[x, y], [dx, dy]] = bezier(b.curve, paramAtDistance(b.lengths, t));

            // léger balancement, lent et régulier (une oscillation sur tout le vol)
            const len = Math.hypot(dx, dy) || 1;
            const sway = b.sway.amp * Math.sin(Math.PI * 2 * t + b.sway.phase);
            const px = x + (-dy / len) * sway;
            const py = y + (dx / len) * sway;

            // la tête suit la direction du vol
            const heading = Math.atan2(dx, -dy) * 180 / Math.PI;
            const rotate = Math.max(-70, Math.min(70, heading * 0.8));

            // apparition en douceur sur les 300 premières millisecondes
            const appear = Math.min(1, (elapsed - b.delay) / 300);
            const ease = appear * appear * (3 - 2 * appear);

            b.svg.style.opacity = ease.toFixed(3);
            b.svg.style.transform =
                `translate3d(${(px - b.size / 2).toFixed(2)}px, ${(py - b.size / 2).toFixed(2)}px, 0) ` +
                `rotate(${rotate.toFixed(2)}deg) scale(${(b.scale * (0.6 + 0.4 * ease)).toFixed(3)})`;
        }

        if (elapsed < DURATION) requestAnimationFrame(frame);
        else layer.remove();
    }

    requestAnimationFrame(frame);
    // filet de sécurité si l'onglet passe en arrière-plan pendant l'animation
    setTimeout(() => layer.remove(), DURATION + 500);
}

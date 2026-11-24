/*
 * La main de « La Création d'Adam », en trame de points violets.
 * Elle pointe le papillon posé sur le titre, s'en approche sans jamais le toucher,
 * et une étincelle apparaît au plus près du geste. L'avant-bras se dissout en poussière.
 *
 * Chaque case de la grille lit son intensité dans une carte de 122 × 61 (hand-data.js),
 * via la transformation inverse de la main (rotation, échelle, position).
 */
import { HAND, HAND_W, HAND_H, TIP, KNUCKLE } from './hand-data.js';

const CELL = 6;
const LEVELS = 10;
const BASE_ANGLE = Math.atan2(TIP.y - KNUCKLE.y, TIP.x - KNUCKLE.x);

const smooth = (e0, e1, x) => { const k = Math.min(1, Math.max(0, (x - e0) / (e1 - e0))); return k * k * (3 - 2 * k); };

// bruit pseudo-aléatoire stable (0..1) pour une case de la carte
function hash(x, y) {
    let h = (x * 374761393 + y * 668265263) | 0;
    h = Math.imul(h ^ (h >>> 13), 1274126177);
    return ((h ^ (h >>> 16)) >>> 0) / 4294967295;
}

function mix(a, b, t) {
    const pa = parseInt(a.slice(1), 16), pb = parseInt(b.slice(1), 16);
    const ch = (s) => [(s >> 16) & 255, (s >> 8) & 255, s & 255];
    const [r1, g1, b1] = ch(pa), [r2, g2, b2] = ch(pb);
    return `rgb(${Math.round(r1 + (r2 - r1) * t)},${Math.round(g1 + (g2 - g1) * t)},${Math.round(b1 + (b2 - b1) * t)})`;
}

export function initHero(section) {
    const canvas = section?.querySelector('[data-hero-canvas]');
    const perch = section?.querySelector('.butterfly');
    if (!canvas || !perch) return;

    const ctx = canvas.getContext('2d');
    const spark = section.querySelector('[data-spark]');
    const css = getComputedStyle(document.documentElement);
    const light = css.getPropertyValue('--violet-light').trim() || '#d9cdfb';
    const deep = css.getPropertyValue('--violet').trim() || '#6a2ee0';
    const palette = Array.from({ length: LEVELS }, (_, i) => mix(light, deep, Math.pow(i / (LEVELS - 1), 0.8)));
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    const hand = { k: 1, cos: 1, sin: 0, fx: 0, fy: 0, t: 0, box: [0, 0, 0, 0] };
    const halo = { x: 0, y: 0, r: 1 };
    const meta = section.querySelector('.hero__meta');
    let W = 0, H = 0, dpr = 1, raf = 0, visible = true, ceil = -1e9;

    function resize() {
        dpr = Math.min(devicePixelRatio || 1, 2);
        W = canvas.clientWidth; H = canvas.clientHeight;
        canvas.width = W * dpr; canvas.height = H * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    // carte -> écran
    function toScreen(u, v) {
        const lx = (u - TIP.x) * hand.k, ly = (v - TIP.y) * hand.k;
        return [hand.fx + lx * hand.cos - ly * hand.sin, hand.fy + lx * hand.sin + ly * hand.cos];
    }

    function update(t) {
        const cr = canvas.getBoundingClientRect(), pr = perch.getBoundingClientRect();
        const tx = pr.left - cr.left + pr.width * 0.45;
        const ty = pr.top - cr.top + pr.height * 0.55;

        const narrow = W < 900;
        // sur petit écran, le bras monte jusqu'aux lignes du haut : il s'efface juste en dessous
        ceil = narrow && meta ? meta.getBoundingClientRect().bottom - cr.top + 6 : -1e9;
        let dx = narrow ? -1 : -1, dy = narrow ? 0.38 : (W < 1300 ? 0.7 : 0.16);
        const n = Math.hypot(dx, dy); dx /= n; dy /= n;
        const k = (narrow ? Math.min(W * 0.75, 420) : Math.min(W * 0.36, 600)) / HAND_W;

        // la main s'approche lentement, s'arrête juste avant de toucher, puis recule
        const reach = (Math.sin(t * 0.7) + 1) / 2;
        const gap = 16 + 34 * (1 - reach * reach);
        const fl = Math.sin(t * 0.9) * 4;
        const fx = tx - dx * gap - dy * fl, fy = ty - dy * gap + dx * fl;
        const a = Math.atan2(dy, dx) - BASE_ANGLE + Math.sin(t * 0.5) * 0.025;

        Object.assign(hand, { k, cos: Math.cos(a), sin: Math.sin(a), fx, fy, t });
        const pts = [[0, 0], [HAND_W, 0], [0, HAND_H], [HAND_W, HAND_H]].map(([u, v]) => toScreen(u, v));
        hand.box = [
            Math.min(...pts.map((p) => p[0])), Math.min(...pts.map((p) => p[1])),
            Math.max(...pts.map((p) => p[0])), Math.max(...pts.map((p) => p[1])),
        ];

        if (spark) {
            const sp = Math.max(0, (reach - 0.75) / 0.25);
            const sx = fx + dx * gap * 0.5, sy = fy + dy * gap * 0.5;
            spark.setAttribute('opacity', (sp * 0.9).toFixed(2));
            spark.setAttribute('transform', `translate(${sx.toFixed(1)} ${sy.toFixed(1)}) rotate(${(t * 40) % 360}) scale(${(0.5 + 0.6 * sp).toFixed(2)})`);
        }

        [halo.x, halo.y] = toScreen(62, 26);
        halo.r = 30 * k * (1 + 0.04 * Math.sin(t * 1.4));
    }

    // écran -> intensité de la carte (interpolation bilinéaire)
    function handField(x, y) {
        const b = hand.box;
        if (x < b[0] || x > b[2] || y < b[1] || y > b[3]) return 0;
        const lx = x - hand.fx, ly = y - hand.fy;
        const u = (lx * hand.cos + ly * hand.sin) / hand.k + TIP.x;
        const v = (-lx * hand.sin + ly * hand.cos) / hand.k + TIP.y;
        if (u < 0 || v < 0 || u >= HAND_W - 1 || v >= HAND_H - 1) return 0;
        const i = u | 0, j = v | 0, fu = u - i, fv = v - j, o = j * HAND_W + i;
        const top = HAND[o] * (1 - fu) + HAND[o + 1] * fu;
        const bot = HAND[o + HAND_W] * (1 - fu) + HAND[o + HAND_W + 1] * fu;
        let val = (top * (1 - fv) + bot * fv) / 255;
        if (val <= 0.04) return 0;
        val = Math.min(1, 0.1 + val * 1.15);

        // fin du bras : il se dissout en poussière de lumière
        const fade = 1 - smooth(HAND_W * 0.62, HAND_W * 0.99, u);
        if (fade < 1) {
            const nz = hash(Math.round(u * 1.6), Math.round(v * 1.6));
            const twinkle = 0.5 + 0.5 * Math.sin(hand.t * 1.7 + nz * 6.283);
            if (nz * 0.75 + twinkle * 0.25 > Math.pow(fade, 0.7)) return 0;
            val *= 0.35 + 0.65 * fade;
        }
        return val;
    }

    function haloField(x, y) {
        const d2 = (x - halo.x) ** 2 + (y - halo.y) ** 2;
        return 0.14 * Math.exp(-d2 / (2 * halo.r * halo.r));
    }

    // Garde-fou pour les machines modestes : on mesure le coût réel d'une image.
    // Trop lent pour 60 images/s : on passe à 30. Vraiment trop lent : la main se fige
    // sur sa pose actuelle, la page reste fluide.
    let cost = 0, frames = 0, tick = 0, halfRate = false, frozen = false;

    function draw(time) {
        raf = 0;
        if (halfRate && (tick++ & 1)) {
            if (!reduce && visible && !frozen) raf = requestAnimationFrame(draw);
            return;
        }
        const startedAt = performance.now();
        update(time / 1000);
        ctx.clearRect(0, 0, W, H);

        // On ne parcourt que la zone de la main et de son halo.
        const pad = halo.r * 2.6;
        const x0 = Math.max(0, Math.floor((Math.min(hand.box[0], halo.x - pad)) / CELL));
        const y0 = Math.max(0, Math.floor((Math.min(hand.box[1], halo.y - pad)) / CELL));
        const x1 = Math.min(Math.ceil(W / CELL), Math.ceil(Math.max(hand.box[2], halo.x + pad) / CELL));
        const y1 = Math.min(Math.ceil(H / CELL), Math.ceil(Math.max(hand.box[3], halo.y + pad) / CELL));

        const paths = Array.from({ length: LEVELS }, () => new Path2D());
        for (let j = y0; j < y1; j++) {
            const y = j * CELL;
            const under = smooth(ceil, ceil + 48, y);
            if (under === 0) continue;
            for (let i = x0; i < x1; i++) {
                const x = i * CELL;
                const f = under * Math.max(handField(x + CELL / 2, y + CELL / 2), haloField(x, y));
                if (f < 0.06) continue;
                const s = CELL * (0.25 + 0.6 * f);
                const o = (CELL - s) / 2;
                paths[Math.min(LEVELS - 1, Math.floor(f * LEVELS))].rect(x + o, y + o, s, s);
            }
        }
        paths.forEach((p, l) => { ctx.fillStyle = palette[l]; ctx.fill(p); });

        const spent = performance.now() - startedAt;
        cost = frames === 0 ? spent : cost * 0.9 + spent * 0.1;
        if (++frames === 45) {
            if (cost > 20) frozen = true;
            else if (cost > 9) halfRate = true;
        }

        if (!reduce && visible && !frozen) raf = requestAnimationFrame(draw);
    }

    const start = () => { if (!raf) raf = requestAnimationFrame(draw); };

    new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        if (visible) start();
    }).observe(section);

    document.addEventListener('visibilitychange', () => { if (!document.hidden && visible) start(); });

    let resizeTimer;
    addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => { resize(); if (reduce) draw(performance.now()); }, 100);
    });

    resize();
    // Attendre les polices : la position du papillon en dépend.
    (document.fonts?.ready ?? Promise.resolve()).then(() => draw(reduce ? 4200 : performance.now()));
}

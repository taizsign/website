/*
 * Transition entre les pages : la page s'efface case par case depuis le point de clic,
 * avec un liseré violet sur le front de l'effacement ; la suivante apparaît de la même façon.
 */
const CELL = 24;
const COVER_MS = 460;
const REVEAL_MS = 560;

function makeCanvas() {
    const canvas = document.createElement('canvas');
    canvas.className = 'wipe';
    canvas.setAttribute('aria-hidden', 'true');
    const dpr = Math.min(devicePixelRatio || 1, 2);
    canvas.width = innerWidth * dpr;
    canvas.height = innerHeight * dpr;
    const ctx = canvas.getContext('2d');
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    document.body.appendChild(canvas);
    return { canvas, ctx };
}

/** Prépare une grille de cases avec, pour chacune, un instant de départ entre 0 et 1. */
function grid(ox, oy, spread) {
    const cols = Math.ceil(innerWidth / CELL), rows = Math.ceil(innerHeight / CELL);
    const max = Math.hypot(Math.max(ox, innerWidth - ox), Math.max(oy, innerHeight - oy));
    const cells = [];
    for (let j = 0; j < rows; j++) {
        for (let i = 0; i < cols; i++) {
            const cx = i * CELL + CELL / 2, cy = j * CELL + CELL / 2;
            const d = Math.hypot(cx - ox, cy - oy) / max;
            // une case sur cinq seulement porte le liseré violet
            cells.push({ x: i * CELL, y: j * CELL, start: d * (1 - spread) + Math.random() * spread, accent: Math.random() < 0.2 });
        }
    }
    return cells;
}

/**
 * @param {boolean} covering true : la page se couvre ; false : elle se découvre
 */
function animate(ctx, cells, duration, covering, done) {
    const violet = getComputedStyle(document.documentElement).getPropertyValue('--violet').trim() || '#6a2ee0';
    const cellTime = 0.35;
    const t0 = performance.now();

    function frame(now) {
        const t = Math.min(1, (now - t0) / duration);
        ctx.clearRect(0, 0, innerWidth, innerHeight);
        const white = new Path2D(), edge = new Path2D();

        for (const c of cells) {
            // progression propre à la case, de 0 à 1
            let p = (t - c.start * (1 - cellTime)) / cellTime;
            p = Math.min(1, Math.max(0, p));
            const fill = covering ? p : 1 - p;
            if (fill <= 0) continue;
            const s = CELL * fill + 0.6;
            const o = (CELL - s) / 2;
            if (c.accent && p > 0 && p < 0.4) edge.rect(c.x + o + s * 0.25, c.y + o + s * 0.25, s * 0.5, s * 0.5);
            else white.rect(c.x + o, c.y + o, s, s);
        }
        ctx.fillStyle = '#ffffff';
        ctx.fill(white);
        ctx.fillStyle = violet;
        ctx.fill(edge);

        if (t < 1) requestAnimationFrame(frame);
        else done();
    }
    requestAnimationFrame(frame);
}

function isInternal(link, event) {
    if (!link || event.defaultPrevented || event.button !== 0) return false;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
    if (link.target && link.target !== '_self') return false;
    if (link.hasAttribute('download') || link.dataset.noWipe !== undefined) return false;
    const url = new URL(link.href, location.href);
    if (url.origin !== location.origin) return false;
    if (url.pathname.startsWith('/documents/')) return false;
    if (url.pathname === location.pathname && url.search === location.search) return false; // ancre sur la même page
    return true;
}

export function initWipe() {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const root = document.documentElement;

    // Arrivée : la page est masquée par boot.js, on la découvre.
    if (root.classList.contains('is-arriving')) {
        const { canvas, ctx } = makeCanvas();
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, innerWidth, innerHeight);
        root.classList.add('is-revealing');
        root.classList.remove('is-arriving');
        animate(ctx, grid(innerWidth / 2, 0, 0.45), REVEAL_MS, false, () => canvas.remove());
    }

    if (reduce) return;

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!isInternal(link, event)) return;

        event.preventDefault();
        const { ctx } = makeCanvas();
        animate(ctx, grid(event.clientX, event.clientY, 0.3), COVER_MS, true, () => {
            try { sessionStorage.setItem('wipe', '1'); } catch (e) { /* tant pis */ }
            location.href = link.href;
        });
    });

    // Retour arrière depuis le cache du navigateur : retirer un éventuel voile resté en place.
    addEventListener('pageshow', (e) => {
        if (e.persisted) document.querySelectorAll('.wipe').forEach((c) => c.remove());
    });
}

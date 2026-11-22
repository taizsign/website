/*
 * La trame de fond du site : une grille de points gris très légers,
 * et un halo autour du curseur où les points grossissent et passent au violet.
 * Le dessin ne tourne que quand il y a quelque chose à animer.
 */

const CELL = 12;          // écart entre deux points
const RADIUS = 170;       // rayon du halo
const LEVELS = 6;

export function initField(canvas) {
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const css = getComputedStyle(document.documentElement);
    const violet = css.getPropertyValue('--violet').trim() || '#6a2ee0';
    const [r, g, b] = [16, 8, 0].map((s) => (parseInt(violet.slice(1), 16) >> s) & 255);
    const tints = Array.from({ length: LEVELS }, (_, i) => `rgba(${r},${g},${b},${(0.1 + 0.34 * (i + 1) / LEVELS).toFixed(3)})`);
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const touch = matchMedia('(hover: none)').matches;

    let W = 0, H = 0, dpr = 1, base = null, raf = 0, idleFrames = 0;
    const mouse = { x: -1e4, y: -1e4, tx: -1e4, ty: -1e4, a: 0, ta: 0 };

    function resize() {
        dpr = Math.min(devicePixelRatio || 1, 2);
        W = innerWidth; H = innerHeight;
        if (!W || !H) return; // fenêtre sans taille (onglet caché au chargement) : on attendra le prochain resize
        canvas.width = W * dpr; canvas.height = H * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        // La grille fixe est dessinée une fois dans un canvas hors écran.
        base = document.createElement('canvas');
        base.width = canvas.width; base.height = canvas.height;
        const bctx = base.getContext('2d');
        bctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        bctx.fillStyle = 'rgba(13,13,16,0.11)';
        const p = new Path2D();
        for (let y = CELL / 2; y < H; y += CELL) {
            for (let x = CELL / 2; x < W; x += CELL) p.rect(x - 0.6, y - 0.6, 1.2, 1.2);
        }
        bctx.fill(p);
        draw(performance.now());
    }

    function draw(time) {
        raf = 0;
        if (!base || !base.width || !base.height) return;
        mouse.x += (mouse.tx - mouse.x) * 0.16;
        mouse.y += (mouse.ty - mouse.y) * 0.16;
        mouse.a += (mouse.ta - mouse.a) * 0.08;

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(base, 0, 0);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        if (mouse.a > 0.01) {
            const R = RADIUS * (1 + 0.05 * Math.sin(time / 450));
            const paths = Array.from({ length: LEVELS }, () => new Path2D());
            const x0 = Math.max(CELL / 2, Math.floor((mouse.x - R) / CELL) * CELL + CELL / 2);
            const y0 = Math.max(CELL / 2, Math.floor((mouse.y - R) / CELL) * CELL + CELL / 2);
            for (let y = y0; y < Math.min(H, mouse.y + R); y += CELL) {
                for (let x = x0; x < Math.min(W, mouse.x + R); x += CELL) {
                    const d2 = (x - mouse.x) ** 2 + (y - mouse.y) ** 2;
                    if (d2 > R * R) continue;
                    const k = 1 - d2 / (R * R);
                    const v = k * k * mouse.a;
                    if (v < 0.04) continue;
                    const s = 1.4 + 4.2 * v;
                    paths[Math.min(LEVELS - 1, Math.floor(v * LEVELS))].rect(x - s / 2, y - s / 2, s, s);
                }
            }
            paths.forEach((p, i) => { ctx.fillStyle = tints[i]; ctx.fill(p); });
        }

        // On continue tant que le halo vit ou bouge ; sinon on s'arrête.
        const moving = Math.abs(mouse.tx - mouse.x) + Math.abs(mouse.ty - mouse.y) > 0.5 || Math.abs(mouse.ta - mouse.a) > 0.01;
        idleFrames = moving ? 0 : idleFrames + 1;
        if (!reduce && (mouse.a > 0.01 || moving) && idleFrames < 600) schedule();
    }

    function schedule() {
        if (!raf) raf = requestAnimationFrame(draw);
    }

    if (!touch) {
        addEventListener('pointermove', (e) => {
            if (e.pointerType === 'touch') return;
            mouse.tx = e.clientX; mouse.ty = e.clientY;
            if (mouse.ta === 0) { mouse.x = mouse.tx; mouse.y = mouse.ty; }
            mouse.ta = 1; idleFrames = 0;
            if (reduce) { mouse.x = mouse.tx; mouse.y = mouse.ty; mouse.a = 1; draw(0); } else schedule();
        }, { passive: true });
        document.documentElement.addEventListener('pointerleave', () => { mouse.ta = 0; schedule(); });
    }

    let resizeTimer;
    addEventListener('resize', () => { clearTimeout(resizeTimer); resizeTimer = setTimeout(resize, 120); });
    resize();
}

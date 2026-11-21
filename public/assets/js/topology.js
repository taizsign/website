/*
 * Schémas d'architecture : des paquets circulent le long des liens « flow »,
 * et le survol d'un nœud met en évidence ses liaisons.
 * L'animation ne tourne que lorsque le schéma est visible à l'écran.
 */
const SVG_NS = 'http://www.w3.org/2000/svg';

export function initTopologies(root = document) {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    root.querySelectorAll('svg[data-topo]').forEach((svg) => {
        setupFocus(svg);
        if (!reduce) setupPackets(svg);
    });
}

function setupPackets(svg) {
    const paths = [...svg.querySelectorAll('path[data-flow]')];
    if (!paths.length) return;

    const layer = document.createElementNS(SVG_NS, 'g');
    svg.appendChild(layer);

    // Deux paquets par lien, décalés, à vitesse constante (en pixels du schéma par seconde).
    const packets = [];
    paths.forEach((path, i) => {
        const length = path.getTotalLength();
        for (let k = 0; k < 2; k++) {
            const rect = document.createElementNS(SVG_NS, 'rect');
            rect.setAttribute('width', '7');
            rect.setAttribute('height', '7');
            rect.setAttribute('class', 'topo-packet');
            layer.appendChild(rect);
            packets.push({ path, length, rect, offset: (i * 0.37 + k * 0.5) % 1, speed: 120 + (i % 3) * 25 });
        }
    });

    let raf = 0, visible = false;
    function frame(now) {
        const t = now / 1000;
        packets.forEach((p) => {
            const progress = (p.offset + (t * p.speed) / p.length) % 1;
            const pt = p.path.getPointAtLength(progress * p.length);
            // fondu à l'entrée et à la sortie du lien
            const alpha = Math.min(1, progress * 6, (1 - progress) * 6);
            p.rect.setAttribute('x', (pt.x - 3.5).toFixed(1));
            p.rect.setAttribute('y', (pt.y - 3.5).toFixed(1));
            p.rect.setAttribute('opacity', alpha.toFixed(2));
        });
        raf = visible ? requestAnimationFrame(frame) : 0;
    }

    new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        if (visible && !raf) raf = requestAnimationFrame(frame);
    }).observe(svg);
}

function setupFocus(svg) {
    const nodes = [...svg.querySelectorAll('[data-node]')];
    const links = [...svg.querySelectorAll('.topo-link')];
    if (!nodes.length) return;

    // Retrouver les extrémités de chaque lien depuis son tracé.
    const ends = links.map((link) => {
        const d = link.querySelector('path').getAttribute('d');
        const [x1, y1, x2, y2] = d.match(/-?\d+(\.\d+)?/g).map(Number);
        const near = (x, y) => nodes.find((n) => {
            const r = n.querySelector('rect').getBBox();
            return x >= r.x - 2 && x <= r.x + r.width + 2 && y >= r.y - 2 && y <= r.y + r.height + 2;
        });
        return { link, a: near(x1, y1), b: near(x2, y2) };
    });

    function focus(node) {
        svg.classList.add('has-focus');
        node.classList.add('is-lit');
        ends.forEach(({ link, a, b }) => {
            const on = a === node || b === node;
            link.classList.toggle('is-lit', on);
            if (on) { a?.classList.add('is-lit'); b?.classList.add('is-lit'); }
        });
    }
    function clear() {
        svg.classList.remove('has-focus');
        svg.querySelectorAll('.is-lit').forEach((el) => el.classList.remove('is-lit'));
    }

    nodes.forEach((node) => {
        node.style.cursor = 'default';
        node.addEventListener('pointerenter', () => { clear(); focus(node); });
        node.addEventListener('pointerleave', clear);
    });
}

/*
 * Petites interactions, chacune ne s'active que si son élément est présent dans la page.
 */

/** Copier l'adresse e-mail. */
export function initCopy() {
    document.querySelectorAll('[data-copy]').forEach((button) => {
        // le bouton du mail n'a qu'une icône : pas de texte à changer, seulement sa couleur
        const label = button.querySelector('[data-copy-label]');
        const initial = label?.textContent;
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                if (label) label.textContent = button.dataset.copied || 'Copié ✓';
                button.classList.add('is-done');
            } catch {
                if (label) label.textContent = button.dataset.copy;
            }
            setTimeout(() => { if (label) label.textContent = initial; button.classList.remove('is-done'); }, 2200);
        });
    });
}

/** Heure de Besançon dans le pied de page, mise à jour chaque minute. */
export function initClock() {
    const clock = document.querySelector('[data-clock]');
    if (!clock) return;
    const format = new Intl.DateTimeFormat('fr-FR', { hour: '2-digit', minute: '2-digit', timeZone: 'Europe/Paris' });
    const tick = () => { clock.textContent = format.format(new Date()); };
    tick();
    setInterval(tick, 30000);
}

/**
 * Le lapin du pied de page.
 *
 * Le pied de page est vu comme un petit niveau de plateformes : la ligne du bas
 * sert de sol, et chaque ligne de texte (titre, adresse, phrase, heure) devient
 * une plateforme sur laquelle il peut sauter, gambader puis redescendre.
 *
 * Tout est calculé image par image : un état (position, écrasement, angle des
 * oreilles...) que de petites actions font évoluer. Les bonds suivent une vraie
 * parabole, avec un temps d'appel au départ et un amorti à l'arrivée. Une
 * « humeur » qui varie lentement règle la longueur et l'enchaînement des bonds.
 */
export function initBunny() {
    const foot = document.querySelector('.foot');
    const bunny = foot?.querySelector('.bunny');
    const svg = bunny?.querySelector('.bunny__body');
    const groundLine = foot?.querySelector('.foot__bottom');
    if (!svg || !groundLine || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const part = (name) => svg.querySelector(`[data-part="${name}"]`);
    const ears = part('ears');
    const backEar = part('back-ear');
    const leg = part('leg');
    const head = part('head');
    const eye = part('eye');

    const GRAVITY = 1250; // px/s²
    const MAX_CLIMB = 170; // hauteur maximale qu'il ose franchir d'un bond
    const rand = (min, max) => min + Math.random() * (max - min);
    const chance = (p) => Math.random() < p;
    const lerp = (a, b, t) => a + (b - a) * t;
    const clamp = (v, min, max) => Math.min(max, Math.max(min, v));
    const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
    const pick = (choices) => {
        let roll = Math.random() * choices.reduce((sum, [weight]) => sum + weight, 0);
        for (const [weight, action] of choices) {
            if ((roll -= weight) <= 0) return action;
        }
        return choices[0][1];
    };

    /* ---------- le niveau : le sol et les lignes de texte ---------- */

    let platforms = [];
    let ground = null;
    const HALF = 18; // demi-largeur des pattes, pour ne pas tomber du bord

    function measure() {
        const box = foot.getBoundingClientRect();
        const line = groundLine.getBoundingClientRect();
        ground = { x1: line.left - box.left, x2: line.right - box.left, y: line.top - box.top, ground: true };

        const found = [];
        foot.querySelectorAll('.foot__title, .foot__address, .foot__note, .foot__time').forEach((el) => {
            const size = parseFloat(getComputedStyle(el).fontSize);
            const range = document.createRange();
            range.selectNodeContents(el);
            // on regroupe les morceaux de texte par ligne
            const lines = [];
            for (const r of range.getClientRects()) {
                if (r.width < 4 || r.height < 4) continue;
                const top = r.top - box.top;
                const same = lines.find((l) => Math.abs(l.top - top) < size * .5);
                if (same) { same.x1 = Math.min(same.x1, r.left - box.left); same.x2 = Math.max(same.x2, r.right - box.left); }
                else lines.push({ top, x1: r.left - box.left, x2: r.right - box.left });
            }
            // il se pose sur le haut des minuscules, pas au-dessus de la boîte du texte
            lines.forEach((l) => {
                if (l.x2 - l.x1 > HALF * 3) found.push({ x1: l.x1, x2: l.x2, y: l.top + size * .43 });
            });
        });
        platforms = [ground, ...found];
    }

    /* ---------- l'état et son rendu ---------- */

    const s = { x: 0, y: 0, dir: 1, rot: 0, sx: 1, sy: 1, ear: 0, earLag: 0, leg: 0, head: 0, blink: 0, alpha: 0 };
    let on = null; // plateforme sous ses pattes

    function render() {
        bunny.style.transform = `translate(${(s.x - bunny.offsetWidth / 2).toFixed(2)}px, ${(s.y - bunny.offsetHeight).toFixed(2)}px)`;
        bunny.style.opacity = s.alpha.toFixed(3);
        svg.style.transform = `scaleX(${s.dir}) rotate(${s.rot.toFixed(2)}deg) scale(${s.sx.toFixed(3)}, ${s.sy.toFixed(3)})`;
        ears.setAttribute('transform', `rotate(${s.ear.toFixed(2)} 31 13)`);
        backEar.setAttribute('transform', `rotate(${(s.earLag - 14).toFixed(2)} 28.5 6)`);
        leg.setAttribute('transform', `rotate(${s.leg.toFixed(2)} 15 31)`);
        head.setAttribute('transform', `rotate(${s.head.toFixed(2)} 29 25)`);
        eye.setAttribute('transform', `translate(0 ${(19 * s.blink).toFixed(2)}) scale(1 ${(1 - s.blink * .9).toFixed(3)})`);
    }

    /** Fait varier l'état pendant `ms`, `step` reçoit t de 0 à 1. */
    const tween = (ms, step) => new Promise((resolve) => {
        const start = performance.now();
        const frame = (now) => {
            const t = Math.min(1, (now - start) / ms);
            step(t);
            render();
            if (t < 1) requestAnimationFrame(frame);
            else resolve();
        };
        requestAnimationFrame(frame);
    });

    // ressort amorti : part de `from`, oscille et revient à 0
    const spring = (from, t, freq = 3, damping = 5) => from * Math.exp(-damping * t) * Math.cos(freq * Math.PI * 2 * t);

    /* ---------- les mouvements ---------- */

    /** Un bond de (x, y) vers (tx, ty), qui peut monter ou descendre. */
    async function jump(tx, ty, lift) {
        const x0 = s.x;
        const y0 = s.y;
        const climb = y0 - ty; // > 0 quand il monte
        s.dir = tx >= x0 ? 1 : -1;
        const big = Math.abs(climb) > 20;

        // l'appel, plus marqué avant un grand saut
        await tween(big ? rand(150, 220) : rand(70, 130), (t) => {
            const e = Math.sin(t * Math.PI / 2);
            s.sy = lerp(1, big ? .72 : .8, e); s.sx = lerp(1, big ? 1.16 : 1.1, e);
            s.ear = lerp(s.ear, 12, e); s.head = lerp(0, 5, e);
        });

        // une parabole qui passe au-dessus du point le plus haut
        const arc = lift + Math.max(0, climb) * .35;
        const peak = Math.max(0, climb) + arc; // hauteur atteinte au-dessus du départ
        const up = Math.sqrt(2 * peak / GRAVITY);
        const down = Math.sqrt(2 * (peak - climb) / GRAVITY);
        const flight = (up + down) * 1000;
        const split = up / (up + down);
        await tween(flight, (t) => {
            const top = y0 - peak;
            s.x = lerp(x0, tx, t);
            s.y = t < split
                ? y0 - peak * (1 - (1 - t / split) ** 2)
                : top + (ty - top) * ((t - split) / (1 - split)) ** 2;
            const vy = t < split ? 1 - t / split : -(t - split) / (1 - split); // +1 en montée, -1 en chute
            s.rot = -vy * lerp(8, 20, Math.min(1, peak / 40));
            s.sy = 1 + .14 * Math.abs(vy) - .04;
            s.sx = 1 / s.sy;
            s.ear = 22 * vy + 6;
            s.earLag = 8 * Math.sin(t * Math.PI * 2);
            s.leg = 38 * Math.sin(t * Math.PI);
            s.head = -4 * vy;
        });
        s.x = tx; s.y = ty;

        // l'arrivée : écrasement puis rebond amorti, les oreilles retombent en avant
        const fall = Math.max(peak - climb, 4);
        const impact = lerp(.14, .32, Math.min(1, fall / 60));
        await tween(rand(220, 320), (t) => {
            s.sy = 1 - spring(impact, t, 1.6, 4.5);
            s.sx = 1 / Math.sqrt(s.sy);
            s.rot = spring(5, t, 1.2, 6);
            s.ear = spring(-16, t, 2.2, 4);
            s.earLag = spring(-6, t, 2.6, 4);
            s.leg = lerp(s.leg, 0, Math.min(1, t * 3));
            s.head = spring(-3, t, 1.5, 5);
        });
        Object.assign(s, { sx: 1, sy: 1, rot: 0, leg: 0 });
    }

    async function sniff() {
        for (let i = Math.round(rand(2, 5)); i > 0; i--) {
            await tween(rand(140, 220), (t) => { s.head = 7 * Math.sin(t * Math.PI); s.ear = -3 * Math.sin(t * Math.PI); });
            await wait(rand(60, 350));
        }
    }
    const twitch = () => tween(rand(260, 380), (t) => { s.earLag = spring(-14, t, 3, 6); s.ear = spring(-5, t, 2, 7); });
    async function sitUp(ms) {
        await tween(220, (t) => { s.rot = lerp(0, -16, t); s.sy = lerp(1, 1.06, t); s.ear = lerp(0, -6, t); });
        await wait(ms);
        await tween(240, (t) => { s.rot = lerp(-16, 0, t); s.sy = lerp(1.06, 1, t); s.ear = lerp(-6, 0, t); });
    }
    async function lookAround() {
        s.dir *= -1; render();
        await wait(rand(500, 1300));
        if (chance(.35)) await sniff();
        if (chance(.6)) { s.dir *= -1; render(); }
    }
    const idle = () => pick([
        [34, () => wait(rand(300, 1500))],
        [22, sniff],
        [16, twitch],
        [12, lookAround],
        [10, () => sitUp(rand(600, 1600))],
        [6, () => wait(rand(1800, 3500))],
    ])();

    /* ---------- la décision : où aller ensuite ---------- */

    /** Plateformes qu'il peut atteindre d'ici, avec le point où il atterrirait. */
    function reachable() {
        return platforms
            .filter((p) => p !== on && Math.abs(p.y - s.y) <= MAX_CLIMB && Math.abs(p.y - s.y) > 12)
            .map((p) => {
                const lo = p.x1 + HALF;
                const hi = p.x2 - HALF;
                if (hi < lo) return null;
                // il vise un point proche, un peu devant lui si possible
                const aim = clamp(s.x + s.dir * rand(20, 70), lo, hi);
                return Math.abs(aim - s.x) <= 150 ? { p, x: aim } : null;
            })
            .filter(Boolean);
    }

    async function wander(mood) {
        // une série de bonds sur la plateforme actuelle
        let count = 1;
        while (count < 8 && chance(lerp(.4, .8, mood))) count++;
        for (let i = 0; i < count; i++) {
            await ready();
            const leap = on.ground && chance(.06 + mood * .06);
            const step = lerp(16, 50, mood) * rand(.7, 1.3) * (leap ? 1.8 : 1);
            let tx = s.x + s.dir * step;
            const lo = on.ground ? -Infinity : on.x1 + HALF;
            const hi = on.ground ? Infinity : on.x2 - HALF;
            if (tx < lo || tx > hi) {
                // au bord d'une ligne de texte : demi-tour, ou il redescend plus tard
                if (!on.ground) { s.dir *= -1; render(); await wait(rand(150, 500)); tx = clamp(s.x + s.dir * step, lo, hi); }
            }
            await jump(tx, on.y, Math.min(30, (5 + step * .34) * rand(.85, 1.2)));
            await wait(leap ? rand(120, 260) : rand(10, 110));
        }
    }

    /* ---------- la boucle ---------- */

    let visible = false;
    let wake = null;
    new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        if (visible && wake) { wake(); wake = null; }
    }).observe(foot);
    async function ready() {
        if (!visible) await new Promise((resolve) => { wake = resolve; });
        while (bunny.matches(':hover')) await wait(120); // il s'arrête pour qu'on lui dise bonjour
    }

    async function enter() {
        measure();
        on = ground;
        Object.assign(s, { x: ground.x1 + HALF, y: ground.y, dir: 1 });
        await tween(400, (t) => { s.alpha = t; });
    }
    async function leave() {
        await tween(350, (t) => { s.alpha = 1 - t; });
        await wait(rand(2500, 8000));
    }

    // si la page change de taille, il redescend sur le sol
    let resized = false;
    addEventListener('resize', () => { resized = true; });
    document.fonts?.ready.then(measure);

    // clignements, indépendants du reste
    (async () => {
        for (;;) {
            await wait(rand(1800, 5200));
            await tween(130, (t) => { s.blink = Math.sin(t * Math.PI); });
            if (chance(.2)) { await wait(90); await tween(120, (t) => { s.blink = Math.sin(t * Math.PI); }); }
        }
    })();

    (async () => {
        let mood = rand(.3, .8);
        await ready();
        await wait(rand(600, 2000));
        await enter();
        for (;;) {
            await ready();
            if (resized) {
                resized = false;
                measure();
                on = ground;
                s.y = ground.y; s.x = clamp(s.x, ground.x1 + HALF, ground.x2 - HALF); render();
            }
            mood = clamp(mood + rand(-.25, .25), .1, 1);

            // au bout du sol, il s'en va et revient par la gauche
            if (on.ground && s.x >= ground.x2 - HALF) {
                await leave();
                await enter();
                continue;
            }

            // sauter sur du texte, ou en redescendre
            const options = reachable();
            const higher = options.filter((o) => o.p.y < s.y);
            const lower = options.filter((o) => o.p.y > s.y);
            if (on.ground && higher.length && chance(.3 + mood * .2)) {
                const o = higher[Math.floor(Math.random() * higher.length)];
                await jump(o.x, o.p.y, rand(14, 26));
                on = o.p;
            } else if (!on.ground && chance(.3)) {
                // plus haut encore, ou retour vers le bas
                const next = chance(.4) && higher.length ? higher : lower.length ? lower : [{ p: ground, x: clamp(s.x + s.dir * 40, ground.x1 + HALF, ground.x2 - HALF) }];
                const o = next[Math.floor(Math.random() * next.length)];
                await jump(o.x, o.p.y, rand(10, 22));
                on = o.p;
            } else {
                // sur le sol, il avance surtout vers la droite
                if (on.ground && s.dir < 0 && chance(.7)) { s.dir = 1; render(); }
                await wander(mood);
            }
            await idle();
        }
    })();
}

/**
 * Les listes derrière les chiffres de l'accueil.
 * La fenêtre s'ouvre au survol du « ? » et reste ouverte tant que la souris
 * ne quitte pas le chiffre ou la fenêtre. La recherche met en violet les noms
 * qui correspondent, même avec une faute de frappe ou sans les accents.
 */
export function initFigures() {
    const figures = document.querySelectorAll('.figure');
    if (!figures.length) return;

    const plain = (text) => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();

    // distance d'édition entre deux mots courts (fautes de frappe)
    function distance(a, b) {
        const row = Array.from({ length: b.length + 1 }, (_, i) => i);
        for (let i = 1; i <= a.length; i++) {
            let prev = row[0];
            row[0] = i;
            for (let j = 1; j <= b.length; j++) {
                const tmp = row[j];
                row[j] = Math.min(row[j] + 1, row[j - 1] + 1, prev + (a[i - 1] === b[j - 1] ? 0 : 1));
                prev = tmp;
            }
        }
        return row[b.length];
    }

    // « de près ou de loin » : morceau du nom, lettres dans l'ordre, ou une faute de frappe
    function matches(name, query) {
        const text = plain(name);
        if (text.includes(query)) return true;
        const words = text.split(/[^a-z0-9#+.]+/).filter(Boolean);
        const tolerance = query.length >= 6 ? 2 : query.length >= 3 ? 1 : 0;
        if (tolerance && words.some((w) => distance(w.slice(0, query.length), query) <= tolerance)) return true;
        if (query.length >= 3) {
            let i = 0;
            for (const ch of text.replace(/\s+/g, '')) if (ch === query[i]) i++;
            if (i === query.length && text.length <= query.length * 3) return true;
        }
        return false;
    }

    figures.forEach((figure) => {
        const help = figure.querySelector('.figure__help');
        const input = figure.querySelector('.figure__search');
        const list = figure.querySelector('.figure__list');
        if (!help || !input || !list) return;
        const items = [...list.children];

        const open = () => { figure.classList.add('is-open'); help.setAttribute('aria-expanded', 'true'); };
        const close = () => {
            figure.classList.remove('is-open');
            help.setAttribute('aria-expanded', 'false');
            if (figure.contains(document.activeElement)) document.activeElement.blur();
        };

        help.addEventListener('pointerenter', open);
        help.addEventListener('click', () => (figure.classList.contains('is-open') ? close() : (open(), input.focus())));
        // la fenêtre est dans le chiffre : quitter l'un sans entrer dans l'autre la ferme
        figure.addEventListener('pointerleave', close);
        figure.addEventListener('keydown', (event) => { if (event.key === 'Escape') { close(); help.focus(); } });
        figure.addEventListener('focusout', (event) => { if (!figure.contains(event.relatedTarget)) figure.classList.remove('is-open'); });

        input.addEventListener('input', () => {
            const query = plain(input.value);
            list.classList.toggle('is-searching', query !== '');
            items.forEach((li) => li.classList.toggle('is-match', query !== '' && matches(li.textContent, query)));
        });
    });
}

/**
 * Au bout de 7 secondes en haut de l'accueil, une flèche dessinée part du J de
 * « Jacquis » et descend vers l'indicateur de défilement : elle se trace, reste
 * une seconde, puis s'en va. Une seule fois, et seulement si l'on n'a pas encore
 * fait défiler la page.
 */
export function initNudge() {
    const nudge = document.querySelector('[data-nudge]');
    const hero = nudge?.closest('.hero');
    const letter = hero?.querySelector('.hero__title > span:nth-child(2) .ch');
    const cue = hero?.querySelector('.hero__scroll');
    if (!nudge || !letter || !cue || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    // le tracé est calculé au moment voulu, d'après la place réelle du J et de l'indicateur
    function trace() {
        const box = hero.getBoundingClientRect();
        const j = letter.getBoundingClientRect();
        const c = cue.getBoundingClientRect();
        const x0 = j.left + j.width * .55 - box.left;
        const y0 = j.bottom + 6 - box.top;
        const x1 = c.left + c.width / 2 - box.left;
        const y1 = c.top - 10 - box.top;
        // un arc qui plonge sous le J, remonte, fait une boucle, puis redescend sur l'indicateur
        const ax = x0 + (x1 - x0) * .62;
        const ay = Math.min(y0, y1) - 64;
        const f = (n) => n.toFixed(1);
        nudge.setAttribute('viewBox', `0 0 ${f(box.width)} ${f(box.height)}`);
        nudge.querySelector('.nudge__shaft').setAttribute('d',
            `M${f(x0)} ${f(y0)} C${f(x0 + 60)} ${f(y0 + 40)} ${f(ax - 130)} ${f(ay + 14)} ${f(ax)} ${f(ay)}`
            + ' c18 -4 22 -24 6 -26 c-14 -2 -18 18 -2 24'
            + ` C${f(ax + 70)} ${f(ay - 8)} ${f(x1 + 8)} ${f(y1 - 56)} ${f(x1)} ${f(y1)}`);
        nudge.querySelector('.nudge__head').setAttribute('d',
            `M${f(x1 - 11)} ${f(y1 - 12)} q5 6 11 12 q4 -8 11 -14`);
    }

    setTimeout(() => {
        if (scrollY > 40 || document.hidden) return;
        trace();
        nudge.classList.add('is-playing');
        nudge.addEventListener('animationend', (event) => {
            if (event.animationName === 'nudge-out') nudge.classList.remove('is-playing');
        });
    }, 7000);
}

/** Barre de progression de la lecture, le long du bord gauche. */
export function initProgress() {
    const bar = document.querySelector('[data-progress]');
    const fill = bar?.querySelector('.progress__fill');
    const dot = bar?.querySelector('.progress__dot');
    if (!fill || !dot) return;
    let queued = false;
    const update = () => {
        queued = false;
        const max = document.documentElement.scrollHeight - innerHeight;
        const ratio = max > 0 ? Math.min(1, Math.max(0, scrollY / max)) : 0;
        fill.style.transform = `scaleY(${ratio})`;
        dot.style.transform = `translateY(${(ratio * bar.clientHeight).toFixed(1)}px)`;
    };
    const schedule = () => { if (!queued) { queued = true; requestAnimationFrame(update); } };
    addEventListener('scroll', schedule, { passive: true });
    addEventListener('resize', schedule);
    update();
}

/**
 * Page projets : une barre qui propose les technologies des projets, avec leur icône.
 * On tape pour réduire la liste (accents et majuscules ignorés), on choisit avec la
 * souris ou les flèches, et seuls les projets qui utilisent la technologie restent.
 */
export function initTechSearch() {
    const box = document.querySelector('[data-techsearch]');
    if (!box) return;
    const input = box.querySelector('.techsearch__input');
    const list = box.querySelector('.techsearch__list');
    const clear = box.querySelector('.techsearch__clear');
    const status = box.querySelector('.techsearch__status');
    const empty = box.querySelector('.techsearch__empty');
    const options = [...list.querySelectorAll('.techsearch__option')];
    const projects = [...document.querySelectorAll('.project[data-techs]')];
    const plain = (text) => text.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    // ce qu'on tape vraiment pour certains noms (« csharp » pour C#, « js » pour JavaScript...)
    const ALIASES = { 'c#': 'csharp c sharp', '.net': 'dotnet', javascript: 'js', 'html / css': 'html css',
        postgresql: 'postgres', 'github actions': 'ci cd', 'vps ovh': 'serveur server', ubuntu: 'linux' };
    const searchable = (tech) => plain(`${tech} ${Object.entries(ALIASES).find(([k]) => tech.toLowerCase().startsWith(k))?.[1] ?? ''}`);
    let active = -1;

    const visible = () => options.filter((o) => !o.hidden);
    const open = () => { list.hidden = false; input.setAttribute('aria-expanded', 'true'); };
    const close = () => { list.hidden = true; input.setAttribute('aria-expanded', 'false'); setActive(-1); };

    function setActive(index) {
        const shown = visible();
        options.forEach((o) => { o.classList.remove('is-active'); o.setAttribute('aria-selected', 'false'); });
        active = index;
        const option = shown[index];
        if (option) {
            option.classList.add('is-active');
            option.setAttribute('aria-selected', 'true');
            option.scrollIntoView({ block: 'nearest' });
            input.setAttribute('aria-activedescendant', option.id);
        } else {
            input.removeAttribute('aria-activedescendant');
        }
    }

    function narrow() {
        const query = plain(input.value);
        options.forEach((o) => { o.hidden = query !== '' && !searchable(o.dataset.tech).includes(query); });
        empty.hidden = visible().length > 0;
        setActive(-1);
    }

    function apply(tech) {
        const key = tech.toLowerCase();
        let count = 0;
        projects.forEach((p) => {
            const match = p.dataset.techs.split('|').includes(key);
            p.classList.toggle('is-filtered', !match);
            if (match) count++;
        });
        input.value = tech;
        clear.hidden = false;
        status.textContent = (count > 1 ? box.dataset.many : box.dataset.one).replace('{n}', count).replace('{tech}', tech);
        close();
    }

    function reset() {
        projects.forEach((p) => p.classList.remove('is-filtered'));
        input.value = '';
        clear.hidden = true;
        status.textContent = '';
        narrow();
    }

    options.forEach((o, i) => {
        o.id = `techsearch-${i}`;
        o.addEventListener('mousedown', (event) => event.preventDefault()); // garder le focus dans le champ
        o.addEventListener('click', () => apply(o.dataset.tech));
    });
    input.addEventListener('focus', () => { narrow(); open(); });
    input.addEventListener('click', open);
    input.addEventListener('input', () => {
        if (!input.value) reset();
        narrow();
        open();
    });
    input.addEventListener('keydown', (event) => {
        const shown = visible();
        if (event.key === 'ArrowDown') { event.preventDefault(); open(); setActive(Math.min(active + 1, shown.length - 1)); }
        else if (event.key === 'ArrowUp') { event.preventDefault(); setActive(Math.max(active - 1, 0)); }
        else if (event.key === 'Enter' && shown[Math.max(active, 0)] && !list.hidden) { event.preventDefault(); apply(shown[Math.max(active, 0)].dataset.tech); }
        else if (event.key === 'Escape') { close(); }
    });
    input.addEventListener('blur', () => setTimeout(close, 120));
    clear.addEventListener('click', () => { reset(); input.focus(); });
}

/** Vidéo YouTube chargée seulement au clic (pas de cookie avant). */
export function initVideo() {
    document.querySelectorAll('[data-video]').forEach((box) => {
        box.querySelector('button')?.addEventListener('click', () => {
            const iframe = document.createElement('iframe');
            iframe.src = `https://www.youtube-nocookie.com/embed/${encodeURIComponent(box.dataset.video)}?autoplay=1&rel=0`;
            iframe.title = 'Démonstration';
            iframe.allow = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;
            box.replaceChildren(iframe);
        });
    });
}

/** Diaporama de l'épreuve E5. */
export function initDeck() {
    const deck = document.querySelector('[data-deck]');
    if (!deck) return;

    const slides = [...deck.querySelectorAll('[data-slide]')];
    const thumbs = [...deck.querySelectorAll('[data-deck-go]')];
    const current = deck.querySelector('[data-deck-current]');
    const stage = deck.querySelector('[data-deck-stage]');
    let index = 0;

    function go(n) {
        slides[index].classList.remove('is-active');
        thumbs[index]?.classList.remove('is-active');
        index = (n + slides.length) % slides.length;
        slides[index].classList.add('is-active');
        thumbs[index]?.classList.add('is-active');
        current.textContent = index + 1;
    }

    deck.querySelector('[data-deck-prev]').addEventListener('click', () => go(index - 1));
    deck.querySelector('[data-deck-next]').addEventListener('click', () => go(index + 1));
    thumbs.forEach((t) => t.addEventListener('click', () => go(+t.dataset.deckGo)));

    const toggleFull = () => (document.fullscreenElement ? document.exitFullscreen() : stage.requestFullscreen?.());
    deck.querySelector('[data-deck-full]').addEventListener('click', toggleFull);
    stage.addEventListener('click', (e) => go(index + (e.offsetX > stage.clientWidth / 3 ? 1 : -1)));

    addEventListener('keydown', (e) => {
        if (e.target.closest('input, textarea, select')) return;
        if (e.key === 'ArrowRight' || e.key === 'PageDown') go(index + 1);
        if (e.key === 'ArrowLeft' || e.key === 'PageUp') go(index - 1);
        if (e.key === 'f' || e.key === 'F') toggleFull();
    });
}

/** Chronologie de la veille : on peut la faire glisser à la souris. */
export function initChrono() {
    const track = document.querySelector('[data-chrono]');
    if (!track) return;

    let down = false, startX = 0, startScroll = 0, moved = false;
    track.addEventListener('pointerdown', (e) => {
        if (e.pointerType !== 'mouse') return;
        down = true; moved = false; startX = e.clientX; startScroll = track.scrollLeft;
    });
    addEventListener('pointermove', (e) => {
        if (!down) return;
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 4) { moved = true; track.classList.add('is-dragging'); }
        track.scrollLeft = startScroll - dx;
    });
    addEventListener('pointerup', () => { down = false; track.classList.remove('is-dragging'); });
    track.addEventListener('click', (e) => { if (moved) e.preventDefault(); }, true);
}

/**
 * Parcours : chaque jalon du profil est relié à sa carte détaillée.
 * Survoler (ou sélectionner au clavier) l'un met l'autre en évidence ;
 * cliquer un jalon amène sa carte à l'écran. Sur petit écran, le profil
 * défile horizontalement : on le cale sur le papillon (aujourd'hui).
 */
export function initProfile() {
    const svg = document.querySelector('[data-profile]');
    if (!svg) return;

    const stops = [...svg.querySelectorAll('[data-step]')];
    const legs = [...document.querySelectorAll('[data-legs] [data-step]')];

    function activate(step) {
        stops.forEach((el) => el.classList.toggle('is-active', el.dataset.step === step));
        legs.forEach((el) => el.classList.toggle('is-active', el.dataset.step === step));
    }

    [...stops, ...legs].forEach((el) => {
        el.addEventListener('pointerenter', () => activate(el.dataset.step));
        el.addEventListener('pointerleave', () => activate(null));
    });

    stops.forEach((stop) => {
        stop.addEventListener('focus', () => activate(stop.dataset.step));
        stop.addEventListener('blur', () => activate(null));
        const open = () => {
            const leg = legs.find((l) => l.dataset.step === stop.dataset.step);
            leg?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            activate(stop.dataset.step);
        };
        stop.addEventListener('click', open);
        stop.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
        });
    });

    const scroller = document.querySelector('[data-profile-scroll]');
    const me = svg.querySelector('.profile__butterfly');
    if (scroller && me && scroller.scrollWidth > scroller.clientWidth) {
        const box = me.getBoundingClientRect(), frame = scroller.getBoundingClientRect();
        scroller.scrollLeft = box.left - frame.left - scroller.clientWidth * 0.6;
    }
}

/** Labo : le détail de l'élément de baie survolé ou sélectionné. */
export function initRack() {
    const rack = document.querySelector('[data-rack]');
    const detail = document.querySelector('[data-rack-detail]');
    if (!rack || !detail) return;

    const initial = detail.textContent;
    const units = [...rack.querySelectorAll('.unit__face[data-note]')];
    let selected = null;

    function show(face) { detail.textContent = face ? face.dataset.note : (selected?.dataset.note ?? initial); }

    units.forEach((face) => {
        face.addEventListener('pointerenter', () => show(face));
        face.addEventListener('pointerleave', () => show(null));
        face.addEventListener('focus', () => show(face));
        face.addEventListener('click', () => {
            selected?.parentElement.classList.remove('is-selected');
            selected = selected === face ? null : face;
            selected?.parentElement.classList.add('is-selected');
            show(selected);
        });
    });
}

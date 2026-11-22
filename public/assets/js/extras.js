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
 * Le lapin du pied de page. Chaque bond a sa propre longueur, sa hauteur et sa durée,
 * avec des pauses de temps en temps : rien ne se répète exactement.
 */
export function initBunny() {
    const meadow = document.querySelector('.meadow');
    const bunny = meadow?.querySelector('.bunny');
    const body = bunny?.querySelector('.bunny__body');
    if (!body || !bunny.animate || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const rand = (min, max) => min + Math.random() * (max - min);
    const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
    const place = (x) => { bunny.style.transform = `translateX(${x}px)`; };

    // on ne l'anime que lorsque le pied de page est à l'écran
    let visible = false;
    let wake = null;
    new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        if (visible && wake) { wake(); wake = null; }
    }).observe(meadow);
    const whenVisible = () => (visible ? Promise.resolve() : new Promise((resolve) => { wake = resolve; }));

    async function hop(from, to) {
        const height = rand(9, 24);
        const duration = rand(330, 520);
        bunny.animate([{ transform: `translateX(${from}px)` }, { transform: `translateX(${to}px)` }],
            { duration, easing: 'cubic-bezier(.3, .1, .5, 1)', fill: 'forwards' });
        await body.animate([
            { transform: 'translateY(0) scale(1.08, .84)' },
            { transform: `translateY(${-height}px) rotate(-9deg) scale(.95, 1.07)`, offset: .42, easing: 'ease-in' },
            { transform: 'translateY(0) rotate(3deg) scale(1.1, .82)', offset: .86 },
            { transform: 'translateY(0) scale(1, 1)' },
        ], { duration, easing: 'ease-out' }).finished;
        place(to);
    }

    async function sniff() {
        const times = Math.round(rand(2, 5));
        for (let i = 0; i < times; i++) {
            await body.animate([
                { transform: 'rotate(0)' },
                { transform: 'rotate(4deg) translateY(1px)' },
                { transform: 'rotate(0)' },
            ], { duration: rand(160, 260) }).finished;
            await wait(rand(80, 400));
        }
    }

    (async () => {
        let x = -60;
        place(x);
        await wait(rand(800, 2500));
        for (;;) {
            await whenVisible();
            if (x > meadow.clientWidth + 10) {
                x = -60;
                place(x);
                await wait(rand(3000, 9000));
                continue;
            }
            // une série de 2 à 6 bonds, puis une pause, parfois pour renifler
            const series = Math.round(rand(2, 6));
            for (let i = 0; i < series; i++) {
                // il s'arrête tant qu'on le survole, le temps de dire bonjour
                while (bunny.matches(':hover')) await wait(120);
                const to = x + rand(24, 62);
                await hop(x, to);
                x = to;
                await wait(rand(30, 160));
            }
            if (Math.random() < .35) await sniff();
            else await wait(rand(250, 1400));
        }
    })();
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

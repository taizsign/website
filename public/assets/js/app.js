/*
 * Point d'entrée JavaScript. Aucune bibliothèque : chaque module s'occupe
 * d'un comportement et ne fait rien si son élément n'est pas dans la page.
 */
import { initField } from './field.js';
import { initNav } from './nav.js';
import { initReveal } from './reveal.js';
import { initTopologies } from './topology.js';
import { initWipe } from './wipe.js';
import { initConsole } from './console.js';
import { initCopy, initClock, initBunny, initFigures, initProgress, initNudge, initVideo, initDeck, initChrono, initProfile, initRack } from './extras.js';
import { initFlight } from './flight.js';
import { initNightlight } from './nightlight.js';
import { initLang } from './lang.js';

// Chaque module est isolé : si l'un plante (navigateur ancien, API absente),
// les autres démarrent quand même et la page reste utilisable.
function safely(name, start) {
    try {
        start();
    } catch (error) {
        console.error(`[${name}]`, error);
    }
}

safely('apparitions', () => initReveal());
document.documentElement.classList.add('is-ready');

safely('transitions', () => initWipe());
safely('filtre', () => initNightlight());
safely('langue', () => initLang());
safely('papillons', () => initFlight());
safely('trame', () => initField(document.getElementById('field')));
safely('navigation', () => initNav());
safely('schémas', () => initTopologies());
safely('console', () => initConsole(document.querySelector('[data-console]')));
safely('copie', () => initCopy());
safely('horloge', () => initClock());
safely('lapin', () => initBunny());
safely('chiffres', () => initFigures());
safely('progression', () => initProgress());
safely('flèche', () => initNudge());
safely('vidéo', () => initVideo());
safely('diaporama', () => initDeck());
safely('chronologie', () => initChrono());
safely('parcours', () => initProfile());
safely('baie', () => initRack());

// La main de la page d'accueil n'est chargée que sur la page d'accueil.
const hero = document.querySelector('[data-hero]');
if (hero) {
    import('./hero.js')
        .then(({ initHero }) => initHero(hero))
        .catch((error) => console.error('[main]', error));
}

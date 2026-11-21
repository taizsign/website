/*
 * La console SQL de la page Compétences.
 * Chaque clic sur une valeur relance la requête côté serveur ; la réponse JSON contient
 * le texte SQL exécuté et le HTML des résultats, rendu par le même gabarit que la page.
 * Sans JavaScript, le formulaire fonctionne tel quel (bouton « Exécuter »).
 */
import { initReveal } from './reveal.js';

export function initConsole(form) {
    if (!form) return;

    const endpoint = form.dataset.endpoint;
    const result = document.querySelector('[data-result]');
    const status = form.querySelector('[data-status]');
    let controller = null;

    // Les noms des champs dépendent de la langue (domaine[] ou domain[], tri ou sort) :
    // on reprend ceux du formulaire, les listes deviennent « a,b,c ».
    function params() {
        const data = new FormData(form);
        const query = new URLSearchParams();
        for (const name of new Set(data.keys())) {
            const values = data.getAll(name).filter(Boolean);
            query.set(name.replace(/\[\]$/, ''), name.endsWith('[]') ? values.join(',') : values[0] || '');
        }
        return query;
    }

    async function run() {
        controller?.abort();
        controller = new AbortController();
        status.classList.add('is-loading');
        const query = params();

        try {
            const response = await fetch(`${endpoint}?${query}`, {
                headers: { Accept: 'application/json' },
                signal: controller.signal,
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            render(await response.json());
            history.replaceState(null, '', `${location.pathname}?${query}`);
        } catch (error) {
            if (error.name !== 'AbortError') form.submit();
        } finally {
            status.classList.remove('is-loading');
        }
    }

    function render(data) {
        status.querySelector('[data-count]').textContent = data.label;
        status.querySelector('[data-ms]').textContent = data.ms.toLocaleString(document.documentElement.lang || 'fr', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        status.querySelector('[data-sql]').textContent = data.sql;

        // HTML produit par le serveur à partir de valeurs échappées (gabarit partials/skill-results.php).
        result.innerHTML = data.html;
        result.classList.remove('is-live');
        void result.offsetWidth; // relance l'animation d'entrée des compétences
        result.classList.add('is-live');
        initReveal(result); // l'en-tête de la matrice (et ses étoiles) apparaît à nouveau
    }

    form.addEventListener('change', run);
    form.addEventListener('submit', (e) => { e.preventDefault(); run(); });
}

/*
 * Choix de la langue. Les drapeaux sont de simples liens vers la même page dans l'autre
 * langue : ils marchent sans JavaScript. Ce module mémorise seulement le choix (cookie d'un an),
 * pour que l'adresse racine du site ouvre directement la bonne version à la prochaine visite.
 */
export function initLang() {
    const nav = document.querySelector('[data-lang]');
    if (!nav) return;

    nav.addEventListener('click', (event) => {
        const link = event.target.closest('[data-lang-to]');
        if (!link) return;
        const secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = `lang=${encodeURIComponent(link.dataset.langTo)}; path=/; max-age=31536000; SameSite=Lax${secure}`;
    });
}

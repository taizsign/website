/*
 * Chargé de façon bloquante dans <head>, avant le premier affichage.
 * - marque <html> avec .js, mais seulement si le navigateur sait lancer les modules
 *   du site : sur un navigateur trop ancien, tout reste affiché, simplement sans animation ;
 * - si on arrive d'une transition de page, masque la page le temps que la trame la découvre ;
 * - filet de sécurité : si les scripts ne démarrent pas, on réaffiche tout au bout de 3 s.
 */
(function () {
    var root = document.documentElement;
    var modules = 'noModule' in document.createElement('script');

    if (!modules) return;
    root.classList.add('js');

    // Filtre de lumière bleue mémorisé : appliqué avant l'affichage, sans flash.
    try {
        if (localStorage.getItem('nightlight') === '1') root.classList.add('night');
    } catch (e) { /* stockage indisponible */ }

    var arriving = false;
    try {
        arriving = sessionStorage.getItem('wipe') === '1';
        sessionStorage.removeItem('wipe');
    } catch (e) { /* stockage indisponible : pas de transition */ }

    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (arriving && !reduce) {
        root.classList.add('is-arriving');
        setTimeout(function () { root.classList.add('is-revealing'); }, 1500);
    }

    // app.js ajoute .is-ready dès que les apparitions sont branchées.
    // S'il ne l'a pas fait à temps (fichier bloqué, erreur), on rend la page visible telle quelle.
    setTimeout(function () {
        if (!root.classList.contains('is-ready')) {
            root.classList.remove('js', 'is-arriving');
            root.classList.add('is-revealing');
        }
    }, 3000);
})();

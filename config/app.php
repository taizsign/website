<?php

/*
 * Configuration du site. Les textes de contenu sont dans database/content/.
 */

return [
    'name'         => 'Raphaël Jacquis',
    'title'        => 'Raphaël Jacquis | systèmes, réseaux et bases de données',
    'description'  => 'Portfolio de Raphaël, étudiant en BTS SIO à Besançon : projets, compétences et homelab, avec un cap sur l\'administration systèmes, réseaux et bases de données.',
    'url'          => 'https://taizsys.xyz',
    'email'        => 'raphaelpv@protonmail.com',
    'github'       => 'https://github.com/taizsign',
    'github_user'  => 'taizsign',
    'location'     => 'Besançon, France',
    'discord'      => 'taizxyz',
    'heading'      => 'Futur administrateur systèmes, réseaux & BDD',  // en haut à droite de l'accueil

    // Affiche le détail des erreurs. Activé automatiquement avec « php -S » (développement).
    'debug' => PHP_SAPI === 'cli-server' || getenv('APP_DEBUG') === '1',

    // Page Labo : sections encore en travaux. Passer à true pour les afficher.
    'lab' => [
        'homelab' => false,  // la baie du homelab
        'probes'  => false,  // le ping en direct vers mes serveurs
    ],

    // Services surveillés sur la page Labo (sonde HTTP côté serveur).
    'uptime' => [
        ['name' => 'mediatek.taizsys.xyz',      'url' => 'https://mediatek.taizsys.xyz/',      'role' => 'Mediatek86 / Symfony'],
        ['name' => 'docs.mediatek.taizsys.xyz', 'url' => 'https://docs.mediatek.taizsys.xyz/', 'role' => 'Documentation / phpDocumentor'],
        ['name' => 'setup.taizsys.xyz',         'url' => 'https://setup.taizsys.xyz/setup.ps1', 'role' => 'Script de déploiement / GitHub Pages'],
    ],

    // Flux lus par la page Veille (lang : langue des articles, ils ne sont pas traduits).
    'feeds' => [
        ['name' => 'IT-Connect',  'url' => 'https://www.it-connect.fr/feed/',              'lang' => 'fr'],
        ['name' => 'GitHub Blog', 'url' => 'https://github.blog/feed/',                    'lang' => 'en'],
        ['name' => 'ZDNet',       'url' => 'https://www.zdnet.fr/feeds/rss/actualites/', 'lang' => 'fr'],
    ],
];

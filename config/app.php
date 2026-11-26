<?php

/*
 * Configuration du site. Les textes de contenu sont dans database/content/.
 */

return [
    'name'         => 'Raphaël Jacquis',
    'title'        => 'Raphaël Jacquis | Portfolio BTS SIO, systèmes et réseaux',
    'description'  => 'Portfolio de Raphaël Jacquis, étudiant en BTS SIO SLAM. Je recherche une alternance en administration systèmes et réseaux à Bordeaux pour la rentrée 2027.',
    'url'          => 'https://raphael-jacquis.fr',
    'email'        => 'raphaelpv@protonmail.com',
    'github'       => 'https://github.com/taizsign',
    'github_user'  => 'taizsign',
    'location'     => 'Besançon, France',
    'mobility'     => 'Bordeaux',  // ville où je cherche l'alternance, affichée sur l'accueil
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
        ['name' => 'mediatek.raphael-jacquis.fr',      'url' => 'https://mediatek.raphael-jacquis.fr/',      'role' => 'Mediatek86 / Symfony'],
        ['name' => 'docs.mediatek.raphael-jacquis.fr', 'url' => 'https://docs.mediatek.raphael-jacquis.fr/', 'role' => 'Documentation / phpDocumentor'],
        ['name' => 'setup.raphael-jacquis.fr',         'url' => 'https://setup.raphael-jacquis.fr/setup.ps1', 'role' => 'Script de déploiement / GitHub Pages'],
    ],

    // Flux lus par la page Veille (lang : langue des articles, ils ne sont pas traduits).
    // match : on ne garde que les titres qui correspondent (facultatif).
    // Les bons plans et les tests produits sont toujours écartés (voir feeds_skip).
    'feeds' => [
        ['name' => 'IT-Connect',  'url' => 'https://www.it-connect.fr/feed/',              'lang' => 'fr'],
        ['name' => 'GitHub Blog', 'url' => 'https://github.blog/feed/',                    'lang' => 'en'],
        ['name' => 'ZDNet',       'url' => 'https://www.zdnet.fr/feeds/rss/actualites/', 'lang' => 'fr',
         'match' => '/\\b((?-i:IA|AI|LLM)|Copilot|ChatGPT|Claude|Gemini|Mistral|OpenAI|Anthropic|agents?|code|développeurs?|open source)\\b/iu'],
    ],
    'feeds_skip' => '/black friday|bon plan|remise|promo|soldes|\\d\\s?€|j\'ai testé|\\btest (du|de|des)\\b|mon top|meilleur|lequel choisir|acheter|cadeaux?|\\bgifts?\\b/iu',
];

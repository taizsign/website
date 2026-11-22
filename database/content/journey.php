<?php

/*
 * Parcours, dans l'ordre où il est présenté en haut de la page.
 *  état : done (terminé) | current (en cours) | next (en projet)
 *  at   : position sur le profil, en année décimale (2024.7 ≈ septembre 2024).
 *         Pour les projets, la date est indicative : elle sert seulement à les placer sur la route.
 */

return [
    [
        'period' => '2022 à 2024',
        'kind'   => 'Diplôme',
        'title'  => 'Bac STI2D',
        'body'   => 'Mathématiques et physique-chimie poussées, premiers pas en algorithmique et en informatique.',
        'tags'   => ['Mathématiques', 'Physique', 'Algorithmique'],
        'state'  => 'done',
        'at'     => 2022.7,
    ],
    [
        'period' => 'Juillet 2025',
        'kind'   => 'Stage · 2 mois',
        'title'  => 'Stage technicien réseaux & systèmes',
        'body'   => 'Diagnostic et réparation de PC, serveurs et périphériques. Installation d\'un pare-feu et d\'un NAS, découverte d\'un réseau d\'entreprise de l\'intérieur, analyse de journaux de sécurité.',
        'tags'   => ['Pare-feu', 'NAS', 'Matériel', 'Journaux'],
        'state'  => 'done',
        'at'     => 2025.55,
    ],
    [
        'period' => 'Depuis 2024',
        'kind'   => 'Formation',
        'title'  => 'BTS SIO SLAM',
        'body'   => 'Services informatiques aux organisations, option SLAM. Le développement d\'applications (C#, PHP, Symfony, SQL), avec un socle commun en systèmes, réseaux et cybersécurité (épreuve E7).',
        'tags'   => ['Développement', 'Systèmes', 'Réseaux', 'Cybersécurité'],
        'state'  => 'current',
        'at'     => 2024.7,
    ],
    [
        'period' => 'En projet',
        'kind'   => 'Certification',
        'title'  => 'TOEIC',
        'body'   => 'Certification d\'anglais professionnel d\'ETS Global : la documentation, les forums et les RFC sont en anglais.',
        'tags'   => ['Anglais'],
        'state'  => 'next',
        'at'     => 2027.2,
    ],
    [
        'period' => 'En projet',
        'kind'   => 'Certification',
        'title'  => 'Certification Cisco',
        'body'   => 'Le CCNA : routage, commutation, VLAN et sécurité réseau sur matériel Cisco. La première marche vers l\'administration réseau.',
        'tags'   => ['Routage', 'Commutation', 'VLAN'],
        'state'  => 'next',
        'at'     => 2027.6,
    ],
];

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
        'body'   => 'Le Bac STI2D m\'a donné des ==bases solides en mathématiques et en physique-chimie==. C\'est aussi là que j\'ai fait mes __premiers pas en algorithmique__, et que l\'informatique est passée d\'une passion à une ==vraie direction==.',
        'tags'   => ['Mathématiques', 'Physique', 'Algorithmique'],
        'state'  => 'done',
        'at'     => 2022.7,
    ],
    [
        'period' => 'Juillet 2025',
        'kind'   => 'Stage de 2 mois',
        'title'  => 'Stage technicien réseaux & systèmes',
        'body'   => 'Deux mois sur le terrain, côté infrastructure. J\'ai ==diagnostiqué et réparé des PC, des serveurs et des périphériques==, installé un __pare-feu__ et un __NAS__, et analysé des __journaux de sécurité__. Surtout, j\'ai ==découvert un réseau d\'entreprise de l\'intérieur==, ce qui m\'a conforté dans l\'envie de me tourner vers l\'administration.',
        'tags'   => ['Pare-feu', 'NAS', 'Matériel', 'Journaux'],
        'state'  => 'done',
        'at'     => 2025.55,
    ],
    [
        'period' => 'Depuis 2024',
        'kind'   => 'Formation',
        'title'  => 'BTS SIO SLAM',
        'body'   => 'Services informatiques aux organisations, option SLAM. J\'y apprends le ==développement d\'applications== en __C#__, __PHP__, __Symfony__ et __SQL__, avec un socle commun en systèmes, réseaux et cybersécurité. Les deux ateliers professionnels m\'ont mené jusqu\'à la ==mise en production sur un serveur==.',
        'tags'   => ['Développement', 'Systèmes', 'Réseaux', 'Cybersécurité'],
        'state'  => 'current',
        'at'     => 2024.7,
    ],
    [
        'period' => 'En projet',
        'kind'   => 'Certification',
        'title'  => 'TOEIC',
        'body'   => 'Je prépare le ==TOEIC== parce qu\'en administration, l\'anglais n\'est pas une option. La __documentation__, les __forums__ et les __RFC__ sont écrits en anglais, et il faut pouvoir les lire sans hésiter.',
        'tags'   => ['Anglais'],
        'state'  => 'next',
        'at'     => 2027.2,
    ],
    [
        'period' => 'En projet',
        'kind'   => 'Certification',
        'title'  => 'Certification Cisco',
        'body'   => 'Le ==CCNA== couvre le __routage__, la __commutation__, les __VLAN__ et la sécurité réseau sur du matériel Cisco. C\'est la ==première vraie marche vers l\'administration réseau==, et la suite logique de mon stage.',
        'tags'   => ['Routage', 'Commutation', 'VLAN'],
        'state'  => 'next',
        'at'     => 2027.6,
    ],
];

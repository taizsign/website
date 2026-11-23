<?php

/*
 * Compétences.
 *  niveau : 0 à 5 (0 = pas encore commencé)
 *  statut : acquis | en cours | objectif
 *  « objectif » = compétences très demandées pour l'administration systèmes, réseaux
 *  et bases de données, que je travaille en ce moment.
 *  preuves : texte libre, ou ['Libellé', 'slug-du-projet'] pour faire un lien.
 *  logo    : nom du fichier dans public/assets/img/logos/ (sans .svg)
 */

return [
    // Systèmes
    ['Linux (Debian, Ubuntu)',            'systèmes', 3, 'acquis',   'Administration au quotidien : services, droits, paquets, journaux.', ['VPS OVH', 'Homelab'], 'linux'],
    ['PowerShell',                        'systèmes', 2, 'acquis',   'Déploiement automatisé de postes : installation et configuration en une exécution.', ['Script setup-computer'], 'powershell'],
    ['Windows (poste client)',            'systèmes', 3, 'acquis',   'Préparation, dépannage et optimisation de postes.', ['Stage technicien', 'Script setup-computer'], 'windows'],
    ['Bash',                              'systèmes', 2, 'en cours', 'Scripts d\'exploitation et de déploiement sur le VPS.', [['Mediatek86', 'mediatek86']], 'gnubash'],
    ['Proxmox VE',                        'systèmes', 2, 'en cours', 'Machines virtuelles et conteneurs LXC.', ['Homelab'], 'proxmox'],
    ['Docker',                            'systèmes', 1, 'en cours', 'Conteneuriser mes services du homelab.', ['Homelab'], 'docker'],
    ['Cybersécurité',                     'systèmes', 2, 'en cours', 'Analyse de journaux et de ports en stage. Projet OPSEC, un poste aussi sûr que possible : puces à risque retirées, Qubes OS, chiffrement VeraCrypt, Mullvad VPN en killswitch avec deux ponts, navigateur LibreWolf.', ['Projet OPSEC', 'Stage technicien', 'Épreuve E7'], 'security'],
    ['Windows Server / Active Directory', 'systèmes', 3, 'acquis',   'Annuaire, GPO, DNS et DHCP en environnement Windows.', ['Homelab', 'Stage technicien'], 'windows'],
    ['Ansible',                           'systèmes', 0, 'objectif', 'Décrire la configuration de mes serveurs plutôt que la refaire à la main.', [], 'ansible'],
    ['Supervision (Zabbix, Grafana)',     'systèmes', 0, 'objectif', 'Savoir qu\'un service tombe avant qu\'un utilisateur me le dise.', [], 'grafana'],
    ['Sauvegarde et PRA',                 'systèmes', 0, 'objectif', 'Règle 3-2-1, Proxmox Backup Server, tests de restauration.', [], 'backup'],

    // Réseaux
    ['Protocoles réseau',                 'réseaux',  3, 'acquis',   'TCP/IP, VLAN, DHCP, DNS, HTTP(S), SMTP, fonctionnement de TLS.', ['Stage technicien'], 'network'],
    ['Matériel et câblage',               'réseaux',  4, 'acquis',   'Diagnostic, réparation, soudure CMS, sertissage RJ45.', ['Stage technicien', 'Atelier perso'], 'hardware'],
    ['nginx',                             'réseaux',  2, 'en cours', 'Hôtes virtuels, PHP-FPM, plusieurs sous-domaines sur un même VPS.', [['MediaTekDocuments', 'mediatekdocuments'], ['Mediatek86', 'mediatek86']], 'nginx'],
    ['Pare-feu OPNsense',                 'réseaux',  1, 'en cours', 'NAT, VLAN, règles de filtrage.', ['Homelab', 'Stage technicien'], 'opnsense'],
    ['Routage et commutation (CCNA)',     'réseaux',  0, 'objectif', 'Préparer le CCNA : routage, commutation, VLAN sur matériel Cisco.', [], 'cisco'],
    ['VPN WireGuard',                     'réseaux',  0, 'objectif', 'Accéder au homelab depuis l\'extérieur sans ouvrir de ports inutiles.', [], 'wireguard'],

    // Bases de données
    ['Modélisation (MCD, MLD)',           'bdd',      4, 'acquis',   'Merise, héritage, cardinalités, passage au modèle relationnel.', [['MediaTekDocuments', 'mediatekdocuments'], ['Mediatek86', 'mediatek86']], 'model'],
    ['MySQL',                             'bdd',      4, 'acquis',   'Jointures, requêtes complexes, triggers, scripts de création.', [['MediaTekDocuments', 'mediatekdocuments'], ['Mediatek86', 'mediatek86']], 'mysql'],
    ['SQLite',                            'bdd',      3, 'acquis',   'Schéma, vues et contraintes CHECK : c\'est la base de ce site.', [['Portfolio', 'portfolio']], 'sqlite'],
    ['Doctrine ORM',                      'bdd',      3, 'acquis',   'Entités, relations, QueryBuilder, migrations.', [['Mediatek86', 'mediatek86']], 'doctrine'],
    ['PostgreSQL',                        'bdd',      1, 'en cours', 'Le SGBD libre le plus demandé en entreprise.', [], 'postgresql'],
    ['Administration de SGBD',            'bdd',      1, 'en cours', 'Droits, sauvegardes et restauration, réplication, optimisation par les index.', [], 'dba'],

    // Développement
    ['PHP',                               'dev',      4, 'acquis',   'PHP 8, POO, PDO. Ce site est écrit en PHP objet sans framework.', [['Mediatek86', 'mediatek86'], ['MediaTekDocuments', 'mediatekdocuments'], ['Portfolio', 'portfolio']], 'php'],
    ['Symfony',                           'dev',      3, 'acquis',   'Contrôleurs, formulaires, Security, Twig.', [['Mediatek86', 'mediatek86']], 'symfony'],
    ['C# / .NET',                         'dev',      3, 'acquis',   'POO, WinForms, architecture MVC, appels d\'API.', [['MediaTekDocuments', 'mediatekdocuments']], 'csharp'],
    ['API REST',                          'dev',      3, 'acquis',   'Conception de routes, JSON, authentification HTTP.', [['MediaTekDocuments', 'mediatekdocuments']], 'api'],
    ['Tests unitaires',                   'dev',      3, 'acquis',   'PHPUnit et MSTest.', [['Mediatek86', 'mediatek86'], ['MediaTekDocuments', 'mediatekdocuments']], 'tests'],
    ['Python',                            'dev',      3, 'acquis',   'Scripts d\'automatisation et manipulation de fichiers.', ['Scripts perso'], 'python'],
    ['JavaScript',                        'dev',      2, 'acquis',   'Interactions et animations sans bibliothèque.', [['Portfolio', 'portfolio']], 'javascript'],

    // Outils
    ['Git / GitHub',                      'outils',   4, 'acquis',   'Branches, pull requests, revue de code.', [['MediaTekDocuments', 'mediatekdocuments'], ['Mediatek86', 'mediatek86']], 'git'],
    ['CI/CD GitHub Actions',              'outils',   2, 'en cours', 'Déploiement automatique en SSH à chaque push.', [['Mediatek86', 'mediatek86']], 'githubactions'],
    ['Microsoft 365',                     'outils',   2, 'acquis',   'Word, Excel, Outlook et Teams au quotidien, en formation comme en stage.', ['Stage technicien'], 'office'],
    ['Prompt engineering (Claude)',       'outils',   2, 'acquis',   'Rédiger des prompts précis, cadrer un agent de code, relire et tester ce qu\'il produit.', [['Portfolio', 'portfolio'], 'Veille technologique'], 'ai'],
    ['Cloud Azure (AZ-900)',              'outils',   0, 'objectif', 'Les bases du cloud public, puis l\'administration Azure.', [], 'azure'],
];

<?php

/*
 * Textes de l'interface en anglais.
 *
 * La clé est le texte français tel qu'il est écrit dans le gabarit (HTML compris :
 * <br>, <em>, &nbsp;…), la valeur sa traduction. Un texte absent d'ici reste en français.
 * Après avoir modifié un gabarit : php bin/console i18n:check
 *
 * Le contenu (projets, compétences, parcours, veille) se traduit dans database/content/en/.
 */

return [

    // Commun : en-tête, pied de page, métadonnées
    'Aller au contenu'                         => 'Skip to content',
    'Accueil'                                  => 'Home',
    'Projets'                                  => 'Projects',
    'Compétences'                              => 'Skills',
    'Parcours'                                 => 'Journey',
    'Veille'                                   => 'Tech watch',
    'Labo'                                     => 'Lab',
    'Me contacter'                             => 'Contact me',
    'Menu'                                     => 'Menu',
    'Navigation principale'                    => 'Main navigation',
    'Navigation mobile'                        => 'Mobile navigation',
    'Langue du site'                           => 'Site language',
    'Activer le filtre de lumière bleue'       => 'Turn on the blue light filter',
    'Désactiver le filtre de lumière bleue'    => 'Turn off the blue light filter',
    'Filtre lumière bleue'                     => 'Blue light filter',
    'Filtre activé, cliquer pour couper'      => 'Filter on, click to turn off',

    'Contact'                                  => 'Contact',
    "Au plaisir d'<em>échanger</em>."        => 'Happy to <em>talk</em>.',
    'Mon code est sur GitHub, sous le nom'     => 'My code lives on GitHub, under the name',
    'On peut aussi me joindre sur Discord,'    => 'You can also reach me on Discord,',
    'Heure locale'                             => 'Local time',
    'Plan du site'                             => 'Site map',
    'Code source'                              => 'Source code',
    'Haut de page'                             => 'Back to top',
    'Adresse copiée ✓'                         => 'Address copied ✓',
    "Copier l'adresse"                         => 'Copy address',
    'Pseudo copié ✓'                           => 'Username copied ✓',
    'Copier le pseudo Discord'                 => 'Copy Discord username',

    // config/app.php
    'Raphaël Jacquis | systèmes, réseaux et bases de données' => 'Raphaël Jacquis | systems, networks and databases',
    "Portfolio de Raphaël, étudiant en BTS SIO à Besançon : projets, compétences et homelab, avec un cap sur l'administration systèmes, réseaux et bases de données."
        => 'Portfolio of Raphaël Jacquis, an IT student in Besançon, France: projects, skills and homelab, heading toward systems, network and database administration.',
    'Futur administrateur systèmes, réseaux & BDD' => 'Future systems, network & database administrator',
    'Script de déploiement / GitHub Pages'     => 'Setup script / GitHub Pages',

    // Accueil
    'Étudiant BTS SIO SLAM'                    => 'IT student, BTS SIO SLAM',
    "J'ai appris à construire des applications et m'oriente maintenant vers ce qui les fait tourner. <em>Je recherche une alternance</em> à Bordeaux pour la rentrée 2027."
        => "I learned how to build applications and I'm now moving toward what keeps them running. <em>I'm looking for a work-study position</em> in Bordeaux, starting in September 2027.",
    'Voir mes projets'                         => 'See my projects',
    'Descendre'                                => 'Scroll down',
    'Qui je suis ?'                            => 'Who am I?',
    "En 2019 j'ai monté mon premier PC. Depuis, l'informatique est devenue une passion. Elle m'a amené au <em>développement</em> et me pousse aujourd'hui vers l'<em>administration systèmes et réseaux</em>. Chez moi tourne un homelab avec NAS, OPNsense, Proxmox. À côté, un PC pensé pour l'<em>OPSEC</em>, dont j'ai retiré les puces à risque. Il démarre sous Kicksecure, chiffre ses données avec VeraCrypt, sort sur Internet par Mullvad."
        => "In 2019 I built my first PC. Since then, computing has become a passion. It led me to <em>development</em> and today pushes me towards <em>systems and network administration</em>. At home I run a homelab with a NAS, OPNsense, Proxmox. Next to it sits a PC built for <em>OPSEC</em>, stripped of its risky chips. It boots Kicksecure, encrypts its data with VeraCrypt, reaches the Internet through Mullvad.",
    "Aujourd'hui"                              => 'Today',
    "<em>Deux applications livrées</em> de bout en bout, de la base MySQL à la mise en production sur mon VPS. Prochaine étape, <em>une alternance</em> pour passer de l'autre côté du serveur."
        => '<em>Two applications delivered</em> end to end, from the MySQL database to production on my own VPS. Next step, <em>a work-study position</em> to move to the other side of the server.',
    'Le cap'                                   => "Where I'm headed",
    "Intégrer <em>une équipe qui gère un vrai parc</em>, entre Windows Server, Linux et réseau Cisco, et y apprendre le métier d'administrateur au quotidien."
        => "Join <em>a team that runs a real fleet</em> of Windows Server, Linux and Cisco networking, and learn the sysadmin job day to day.",
    'À côté'                                   => 'On the side',
    'Je répare mon matériel moi-même, <em>soudure</em> comprise. En parallèle, je prépare le <em>TOEIC</em> ainsi que la certification <em>Cisco CCNA</em>.'
        => "I repair my own hardware, <em>soldering</em> included. Alongside that, I'm preparing for the <em>TOEIC</em> as well as the <em>Cisco CCNA</em> certification.",
    'compétences acquises'                     => 'skills acquired',
    "WHERE statut = 'acquis'"                  => "WHERE status = 'acquired'",
    "en cours d'apprentissage"                 => 'currently learning',
    "WHERE statut = 'en cours'"                => "WHERE status = 'learning'",
    'objectifs pour la suite'                  => 'goals for what comes next',
    "WHERE statut = 'objectif'"                => "WHERE status = 'goal'",
    'outils et technologies dans mes {n} projets' => 'tools and technologies across my {n} projects',
    'Voir la liste'                            => 'Show the list',
    'Rechercher'                               => 'Search',
    'Rechercher dans la liste'                 => 'Search the list',
    'Filtrer par langage ou technologie'       => 'Filter by language or technology',
    'Filtrer les projets par technologie'      => 'Filter projects by technology',
    'Effacer le filtre'                        => 'Clear the filter',
    'Aucune technologie ne correspond.'        => 'No technology matches.',
    '{n} projet utilise {tech}'                => '{n} project uses {tech}',
    '{n} projets utilisent {tech}'             => '{n} projects use {tech}',
    'Ces chiffres sortent de la base SQLite qui fait tourner ce site.' => 'These numbers come straight from the SQLite database that runs this site.',
    'Interroger mes compétences →'             => 'Query my skills →',
    'Du <em>schéma de base</em><br>à la <em>mise en production</em>.' => 'From <em>database schema</em><br>to <em>production</em>.',
    'En ce moment'                             => 'Right now',
    'Ce que je ne sais <em>pas encore</em> faire.' => "What I <em>can't do yet</em>.",
    '[pas encore installé]'                    => '[not installed yet]',
    'paquets à installer.'                     => 'packages to install.',
    "Une liste honnête vaut mieux qu'une barre de progression gonflée. Voici les compétences recherchées en administration systèmes, réseaux et bases de données que je n'ai pas encore, et que je travaille."
        => "An honest list beats an inflated progress bar. These are the in-demand systems, network and database administration skills I don't have yet, and that I'm working on.",
    'Voir le parcours et la suite →'           => 'See my journey and what comes next →',

    // Projets
    'projets'                                  => 'projects',
    "<em>Deux applications complètes</em> construites pendant le BTS, et <em>ce site</em>. Pour chacun, le <em>contexte</em>, l'<em>architecture</em>, des extraits du <em>code source</em> et ce que je <em>referais autrement</em> aujourd'hui."
        => '<em>Two complete applications</em> built during my BTS, and <em>this site</em>. For each one, the <em>context</em>, the <em>architecture</em>, excerpts from the <em>source code</em> and what I would <em>do differently</em> today.',
    'Aussi sur GitHub'                         => 'Also on GitHub',
    'Script PowerShell qui prépare un poste Windows en une commande. Détaillé dans le <span class="u">Labo</span>.'
        => 'PowerShell script that sets up a Windows machine with a single command. Covered in the <span class="u">Lab</span>.',
    'Application Symfony&nbsp;: catalogue de visites, formulaire de contact, authentification et jeux de données de test.'
        => 'Symfony application: a catalogue of trips, a contact form, authentication and test fixtures.',
    'Technologies principales'                 => 'Main technologies',
    'Lire le dossier'                          => 'Read the case study',
    'Année'                                    => 'Year',
    'Cadre'                                    => 'Context',
    "Ce que j'ai fait"                         => 'What I did',
    'Liens'                                    => 'Links',
    'Architecture'                             => 'Architecture',
    'Survolez un élément pour voir ses liaisons.' => 'Hover over a component to see its connections.',
    'Le contexte'                              => 'Context',
    "Ce que j'ai construit"                    => 'What I built',
    'Extraits de code'                         => 'Code excerpts',
    'Du code copié tel quel depuis les dépôts&nbsp;:' => 'Code copied as is from the repositories:',
    'Avec du recul'                            => 'In hindsight',
    'Ce que je<br>referais autrement.'         => 'What I would<br>do differently.',
    "Relu aujourd'hui avec un œil plus systèmes et bases de données. Rien de tout ça n'empêchait le projet de fonctionner&nbsp;; tout ça compterait en production."
        => 'Reread today with a more systems and database minded eye. None of this kept the project from working, but all of it would matter in production.',
    'Stack'                                    => 'Stack',
    'Démonstration'                            => 'Demo',
    'vidéo'                                    => 'video',
    "La vidéo ne se charge qu'au clic&nbsp;: pas de cookie YouTube tant que vous ne l'avez pas demandée."
        => 'The video only loads when you click it, so there is no YouTube cookie until you ask for one.',
    'Lire la démonstration de {title}'         => 'Play the {title} demo',
    'Lire la démonstration'                    => 'Play the demo',
    'Documents'                                => 'Documents',
    'à venir'                                  => 'coming soon',
    'Référentiel BTS SIO'                      => 'BTS SIO framework',
    'Les compétences du référentiel mobilisées par ce projet.' => 'The framework competencies this project puts to work.',
    'Projet suivant'                           => 'Next project',
    "Schéma d'architecture : "                 => 'Architecture diagram: ',
    ' vers '                                   => ' to ',

    // Compétences
    'compétences'                              => 'skills',
    'Mes compétences, stockées dans une base SQLite et interrogeables directement depuis la page.'
        => 'My skills, stored in an SQLite database and queryable right from the page.',
    "Pas de jauges au hasard&nbsp;: mes compétences sont rangées dans une vraie base de données, chacune avec un niveau, un statut et une preuve. Cliquez sur les valeurs de la requête pour filtrer&nbsp;; c'est exactement cette requête que le serveur exécute."
        => 'No random gauges here. My skills are stored in a real database, each with a level, a status and some evidence. Click the values in the query to filter it. This is exactly the query the server runs.',
    'Exécuter'                                 => 'Run',
    'Requête SQL modifiable'                   => 'Editable SQL query',
    'Ordre de tri'                             => 'Sort order',
    "/* 'objectif' : compétences très demandées en systèmes, réseaux et BDD que je travaille en ce moment */"
        => "/* 'goal': in-demand systems, network and database skills I'm working on right now */",
    'en'                                       => 'in',
    'requête exécutée'                         => 'executed query',
    'Systèmes'                                 => 'Systems',
    'Réseaux'                                  => 'Networks',
    'Bases de données'                         => 'Databases',
    'Développement'                            => 'Development',
    'Outils et cloud'                          => 'Tools and cloud',
    'À apprendre'                              => 'To learn',
    'Notions'                                  => 'Basics',
    'En pratique'                              => 'Hands-on',
    'Autonome'                                 => 'Independent',
    'Confirmé'                                 => 'Advanced',
    'Expert'                                   => 'Expert',
    '{n}&nbsp;en cours'                        => '{n}&nbsp;learning',
    'Aucune ligne. La requête est valide, elle ne renvoie simplement rien&nbsp;: réactivez une valeur.'
        => 'No rows. The query is valid, it just returns nothing. Turn a value back on.',
    'Lecture de gauche à droite, du niveau le plus faible au plus élevé.' => 'Read from left to right, from the lowest level to the highest.',
    'Classé du niveau le plus élevé au plus faible.' => 'Sorted from the highest level to the lowest.',
    'Survolez ou touchez une compétence pour en savoir plus.' => 'Hover over or tap a skill to learn more.',
    'Matrice des compétences : domaines en lignes, niveaux en colonnes' => 'Skills matrix: domains in rows, levels in columns',
    'Domaine'                                  => 'Domain',
    '{n} sur {max}'                            => '{n} out of {max}',

    // Parcours
    'parcours'                                 => 'journey',
    "Du bac STI2D au BTS SIO, et la suite : l'administration systèmes, réseaux et bases de données."
        => 'From the STI2D baccalaureate to the BTS SIO, and what comes next: systems, network and database administration.',
    'Systèmes, réseaux & bases de données'     => 'Systems, networks & databases',
    'En projet'                                => 'Planned',
    "Le profil de l'étape"                     => 'The stage profile',
    'terminé'                                  => 'done',
    'en cours'                                 => 'in progress',
    'en projet'                                => 'planned',
    'LE CAP'                                   => 'THE GOAL',
    'Profil du parcours : '                    => 'Journey profile: ',
    ', puis '                                  => ', then ',
    'BTS SIO, épreuve E5'                     => 'BTS SIO, E5 exam',
    'Support de présentation'                  => 'Presentation slides',
    "Les diapositives présentées à l'épreuve, consultables en plein écran." => 'The slides I presented at the exam, viewable full screen.',

    // Épreuve E5
    'Épreuve E5'                               => 'E5 exam',
    "Le support présenté à l'épreuve. Flèches du clavier pour naviguer, <kbd>F</kbd> pour le plein écran."
        => 'The slides presented at the exam. Use the arrow keys to move between slides and <kbd>F</kbd> for full screen.',
    'Les diapositives arrivent bientôt.'       => 'The slides are coming soon.',
    'Diapositive {n}'                          => 'Slide {n}',
    'Diapositive précédente'                   => 'Previous slide',
    'Diapositive suivante'                     => 'Next slide',
    'Plein écran'                              => 'Full screen',
    'Aller à la diapositive {n}'               => 'Go to slide {n}',

    // Veille
    'veille'                                   => 'tech watch',
    "Veille technologique : l'IA générative dans le développement logiciel, de l'assistant à l'agent autonome."
        => 'Tech watch: generative AI in software development, from coding assistant to autonomous agent.',
    'Veille technologique'                     => 'Tech watch',
    'édition 2026'                             => '2026 edition',
    "L'IA générative dans le développement logiciel" => 'Generative AI in software development',
    "De l'assistant de code à l'agent autonome." => 'From coding assistant to autonomous agent.',
    'Problématique'                            => 'Research question',
    'Sous-problématique'                       => 'Follow-up question',
    'Axes de recherche'                        => 'Research areas',
    'Chronologie'                              => 'Timeline',
    'Faites défiler horizontalement.'          => 'Scroll sideways.',
    'EN COURS'                                 => 'NOW',
    'à suivre…'                                => 'to be continued…',
    'Synthèse'                                 => 'Takeaways',
    'Le fil, en direct'                        => 'Live feed',
    'actualisé toutes les heures'              => 'refreshed every hour',
    'Les derniers articles de mes flux, récupérés par le serveur et mis en cache une heure. Votre navigateur ne contacte aucun de ces sites.'
        => 'The latest articles from the feeds I follow, fetched by the server and cached for an hour. Your browser never contacts any of these sites.',
    'Flux indisponible pour le moment.'        => 'Feed unavailable right now.',
    'Sources'                                  => 'Sources',

    // Labo
    'labo'                                     => 'lab',
    'Mon infrastructure en production et mes outils. Le homelab et le ping en direct vers mes serveurs arrivent bientôt.'
        => 'My production infrastructure and my tools. The homelab and a live ping to my servers are coming soon.',
    'Page en travaux'                          => 'Page under construction',
    'En travaux.'                              => 'Under construction.',
    'Prochainement ici&nbsp;:'                 => 'Coming soon:',
    'mon homelab'                              => 'my homelab',
    'et'                                       => 'and',
    'un ping en direct vers mes propres serveurs' => 'a live ping to my own servers',
    'EN TRAVAUX'                               => 'WORK AHEAD',
    'Le côté systèmes et réseau&nbsp;: ce qui tourne déjà en production sous mon nom de domaine, et les outils que je me fabrique.'
        => 'The systems and network side: what already runs in production under my own domain, and the tools I build for myself.',
    'En production'                            => 'In production',
    'Un VPS,<br>un domaine, trois services.'   => 'One VPS,<br>one domain, three services.',
    "Un VPS OVH sous Ubuntu sert plusieurs sous-domaines de taizsys.xyz grâce aux hôtes virtuels nginx. Le code arrive par GitHub&nbsp;: déploiement SSH pour le VPS, GitHub Pages pour le script d'installation."
        => 'An OVH VPS running Ubuntu serves several taizsys.xyz subdomains through nginx virtual hosts. The code comes from GitHub: SSH deployment for the VPS, GitHub Pages for the setup script.',
    'zone DNS'                                 => 'DNS zone',
    'dépôts / Actions / Pages'                 => 'repos / Actions / Pages',
    'hôtes virtuels'                           => 'virtual hosts',
    '2 bases'                                  => '2 databases',
    'A / sous-domaines'                        => 'A / subdomains',
    'déploiement SSH'                          => 'SSH deployment',
    'publie'                                   => 'publishes',
    'VPS OVH / Ubuntu'                         => 'OVH VPS / Ubuntu',
    'API REST'                                 => 'REST API',
    'État des services'                        => 'Service status',
    'relevé toutes les 5 min'                  => 'checked every 5 min',
    "Sonde HTTP lancée par ce serveur. Chaque barre de l'historique est un relevé." => 'HTTP probe run by this server. Each bar in the history is one check.',
    'service'                                  => 'service',
    'état'                                     => 'status',
    'réponse'                                  => 'response',
    'historique'                               => 'history',
    'dispo.'                                   => 'uptime',
    'pas encore relevé'                        => 'not checked yet',
    'en ligne'                                 => 'up',
    'injoignable'                              => 'unreachable',
    'délai dépassé'                            => 'timed out',
    'pas de relevé'                            => 'no data',
    'Homelab'                                  => 'Homelab',
    'La baie,<br>en construction.'             => 'The rack,<br>under construction.',
    "Le homelab, c'est l'endroit où je peux tout casser. Chaque brique sert à pratiquer une compétence d'administration avant d'en avoir besoin pour de vrai. Survolez un élément."
        => "The homelab is where I'm allowed to break everything. Each building block lets me practise an admin skill before I need it for real. Hover over an item.",
    'en place'                                 => 'running',
    'prévu'                                    => 'planned',
    'Survolez ou sélectionnez un élément de la baie.' => 'Hover over or select an item in the rack.',
    ' : '                                      => ': ',
    'Outil maison'                             => 'Homemade tool',
    "Préparer un poste Windows neuf en une seule commande, au lieu d'une heure de clics." => 'Set up a fresh Windows machine with a single command instead of an hour of clicking.',
    'Voir le script sur GitHub ↗'              => 'See the script on GitHub ↗',
    'PowerShell (administrateur)'              => 'PowerShell (administrator)',
    "<strong>Se relance en administrateur</strong> si besoin, et vérifie qu'on est au moins sous Windows 10."
        => '<strong>Relaunches itself as administrator</strong> if needed, and checks that the machine runs at least Windows 10.',
    "<strong>Choisit son gestionnaire de paquets</strong>&nbsp;: winget s'il est présent, sinon installe Chocolatey."
        => "<strong>Picks a package manager</strong>: winget if it's there, otherwise it installs Chocolatey.",
    '<strong>Installe les logiciels et les runtimes</strong>&nbsp;: navigateur, lecteur PDF, suite bureautique, VLC, Visual C++, .NET Desktop Runtime.'
        => '<strong>Installs software and runtimes</strong>: browser, PDF reader, office suite, VLC, Visual C++, .NET Desktop Runtime.',
    "<strong>Applique quelques réglages</strong>&nbsp;: plan d'alimentation haute performance, Game Bar désactivée, télémétrie réduite."
        => '<strong>Applies a few settings</strong>: high performance power plan, Game Bar turned off, reduced telemetry.',
    '<strong>Propose le pilote graphique</strong> NVIDIA ou AMD, au choix.'
        => '<strong>Offers the graphics driver</strong> for NVIDIA or AMD, your choice.',

    // Erreurs
    'Page introuvable'                         => 'Page not found',
    'Erreur'                                   => 'Error',
    'erreur {code}'                            => 'error {code}',
    'destination introuvable'                  => 'destination unreachable',
    'Paquet perdu.'                            => 'Packet lost.',
    "L'adresse <code>{path}</code> ne mène nulle part. Le lien est peut-être ancien, ou la page a déménagé."
        => 'The address <code>{path}</code> leads nowhere. The link may be outdated, or the page has moved.',
    'Le serveur a trébuché.'                   => 'The server tripped.',
    "Une erreur s'est produite de mon côté. Elle est journalisée&nbsp;; réessayez dans un instant."
        => 'Something went wrong on my side. It has been logged, so please try again in a moment.',
    "Retour à l'accueil"                       => 'Back to home',
    'Voir les projets'                         => 'See the projects',

    // Pluriels (View::plural) : forme au singulier et au pluriel
    'acquise'      => 'acquired',     'acquises'      => 'acquired',
    'axe'          => 'area',         'axes'          => 'areas',
    'composant'    => 'component',    'composants'    => 'components',
    'compétence'   => 'skill',
    'dossier'      => 'case study',   'dossiers'      => 'case studies',
    'enseignement' => 'takeaway',     'enseignements' => 'takeaways',
    'extrait'      => 'excerpt',      'extraits'      => 'excerpts',
    'ligne'        => 'row',          'lignes'        => 'rows',
    'objectif'     => 'goal',         'objectifs'     => 'goals',
    'point'        => 'point',        'points'        => 'points',
    'relevé'       => 'check',        'relevés'       => 'checks',
    'réalisation'  => 'feature',      'réalisations'  => 'features',
    'source'       => 'source',       'sources'       => 'sources',
    'technologie'  => 'technology',   'technologies'  => 'technologies',
    'élément'      => 'item',         'éléments'      => 'items',
];

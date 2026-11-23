<?php

/*
 * Projets présentés sur le site.
 * Mise en forme des textes : `code`, **gras**, ligne vide = nouveau paragraphe.
 *
 * Schémas d'architecture : coordonnées dans un repère de 960 × 420.
 * kind : client | server | data | service | external  (change l'apparence du nœud)
 */

$btsCommon = ['B1.1', 'B1.2', 'B1.3', 'B1.4', 'B1.5', 'B1.6', 'B2.1.1', 'B2.1.3', 'B2.1.4', 'B2.2.1', 'B2.2.4', 'B2.2.5', 'B2.3.1', 'B2.3.2'];

return [

    [
        'slug'    => 'mediatekdocuments',
        'title'   => 'MediaTekDocuments',
        'tagline' => 'Un logiciel de gestion de médiathèque qui parle à sa base à travers une API que j\'ai mise en ligne.',
        'summary' => 'Application de bureau C# pour le personnel d\'une médiathèque : livres, DVD, revues, commandes, abonnements. Elle ne touche ==jamais la base directement== : tout passe par une __API REST en PHP__, hébergée sur mon VPS.',
        'year'    => 2026,
        'context' => 'Atelier professionnel 2 · BTS SIO',
        'role'    => 'Client C#, API PHP, base MySQL, déploiement',
        'video'   => null,

        'links' => [
            ['Dépôt de l\'application', 'https://github.com/taizsign/mediatekdocuments'],
            ['Dépôt de l\'API REST', 'https://github.com/taizsign/rest_mediatekdocuments'],
        ],

        'architecture' => [
            'nodes' => [
                ['id' => 'msi',    'x' => 30,  'y' => 300, 'w' => 190, 'label' => 'Installateur .msi',  'sub' => 'déployé sur les postes',     'kind' => 'external'],
                ['id' => 'client', 'x' => 30,  'y' => 150, 'w' => 190, 'label' => 'MediaTekDocuments',  'sub' => 'C# · WinForms · .NET 4.7.2', 'kind' => 'client'],
                ['id' => 'nginx',  'x' => 330, 'y' => 150, 'w' => 130, 'label' => 'nginx',              'sub' => 'serveur web',                'kind' => 'server'],
                ['id' => 'api',    'x' => 560, 'y' => 150, 'w' => 170, 'label' => 'API REST',           'sub' => 'PHP 8.4 · PHP-FPM',          'kind' => 'service'],
                ['id' => 'db',     'x' => 820, 'y' => 150, 'w' => 120, 'label' => 'MySQL',              'sub' => 'mediatek86',                 'kind' => 'data'],
            ],
            'links' => [
                ['from' => 'msi',    'to' => 'client', 'label' => 'installe'],
                ['from' => 'client', 'to' => 'nginx',  'label' => 'HTTP · JSON', 'flow' => true],
                ['from' => 'nginx',  'to' => 'api',    'label' => 'FastCGI', 'flow' => true],
                ['from' => 'api',    'to' => 'db',     'label' => 'PDO', 'flow' => true],
            ],
            'groups' => [
                ['label' => 'VPS OVH · Ubuntu', 'x' => 305, 'y' => 100, 'w' => 645, 'h' => 160],
                ['label' => 'Poste · Windows', 'x' => 10, 'y' => 100, 'w' => 230, 'h' => 290],
            ],
        ],
        'architecture_caption' => 'Le client n\'a ==aucun accès à MySQL== : il envoie des requêtes HTTP (GET, POST, PUT, DELETE) avec un corps JSON à l\'API, qui seule parle à la base. Changer de poste ou de base ne demande donc __aucune modification côté client__.',

        'brief' => [
            ['L\'existant', 'L\'application WinForms de départ est ==à moitié construite==. On peut rechercher et consulter les livres, DVD et revues, et enregistrer la réception d\'un numéro de revue. Pas de connexion, pas de commandes, et une API REST de départ qui ne sait faire que quelques lectures.'],
            ['La demande', 'Faire évoluer l\'outil pour le personnel : __commandes de livres et de DVD__ avec suivi par étapes, __abonnements aux revues__, alerte avant échéance, accès différent selon le service de la personne connectée. Et le tout en ==vrai client-serveur==, avec l\'API déployée sur un serveur.'],
        ],

        'build' => [
            ['Connexion par service', 'À la connexion, l\'application récupère le service de l\'utilisateur et ==adapte ce qu\'il voit== : un service n\'a pas accès du tout, un autre perd certains onglets, et l\'alerte d\'abonnements n\'apparaît que pour les services concernés.'],
            ['Commandes et suivi', 'Une commande de livre ou de DVD avance par étapes. Les ==règles métier sont vérifiées avant l\'envoi== : pas de retour en arrière, pas de règlement d\'une commande qui n\'a pas été livrée, plus de modification une fois réglée.'],
            ['Abonnements et échéances', 'À l\'ouverture, une fenêtre liste les abonnements qui se terminent dans les 30 jours. Le tri et le calcul des dates sont ==faits par MySQL==, pas par le client.'],
            ['API REST complétée', 'Nouvelles routes côté PHP pour les utilisateurs, les étapes de suivi, les commandes et les abonnements. Chaque route passe par une ==requête préparée PDO== et répond en __JSON__.'],
            ['Mise en production', 'API installée sur un ==VPS OVH sous Ubuntu== (nginx, PHP-FPM, MySQL), fichiers envoyés par WinSCP, tests des routes dans Postman. Le client est livré avec un __installateur Windows__.'],
            ['Qualité', '==14 tests unitaires MSTest==, analyse __SonarLint__, journal d\'erreurs côté client dans `%AppData%`, documentation technique générée.'],
        ],

        'retro' => [
            ['Les mots de passe', 'L\'identifiant et le mot de passe partent dans l\'URL d\'une requête GET et sont ==comparés en clair== dans la table `utilisateur`. Une URL finit dans les journaux du serveur. Aujourd\'hui : envoi en __POST__ et mot de passe ==haché== avec `password_hash()` / `password_verify()`.'],
            ['Les numéros de commande', 'L\'API calcule le prochain identifiant avec `MAX(id) + 1`. Deux postes qui valident au même moment peuvent obtenir ==le même numéro==. Une clé `AUTO_INCREMENT` règle la question.'],
            ['Deux écritures, aucune transaction', 'Une commande s\'enregistre en deux `INSERT` (table `commande`, puis `commandedocument` ou `abonnement`). Si le second échoue, il reste ==une commande orpheline==. Il faut un `BEGIN … COMMIT` autour des deux.'],
            ['Le fichier .env', 'Il est ==versionné dans le dépôt public==. Les secrets n\'ont rien à faire dans Git : variables d\'environnement du serveur, et un `.env.example` à la place.'],
        ],

        'excerpts' => [
            [
                'title'    => 'Abonnements qui expirent dans les 30 jours',
                'language' => 'sql',
                'source'   => 'rest_mediatekdocuments · src/MyAccessBDD.php',
                'caption'  => 'Un abonnement est une commande : les deux tables partagent la même clé `id`, d\'où la jointure. Toute la logique de date reste dans la base.',
                'code'     => <<<'SQL'
SELECT c.id, c.dateCommande, c.montant, a.dateFinAbonnement, a.idRevue, d.titre
FROM commande c
JOIN abonnement a ON c.id = a.id
JOIN document d ON a.idRevue = d.id
WHERE a.dateFinAbonnement BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY a.dateFinAbonnement ASC
SQL,
            ],
            [
                'title'    => 'Les règles du suivi de commande',
                'language' => 'csharp',
                'source'   => 'mediatekdocuments · view/FrmMediatek.cs',
                'caption'  => 'Étape 1 : en cours, 2 : livrée, 3 : réglée. Les contrôles sont faits avant d\'appeler l\'API ; avec le recul, l\'API devrait aussi les vérifier, puisqu\'un autre client pourrait l\'appeler.',
                'code'     => <<<'CS'
if (commande.IdSuivi == 3)
{
    MessageBox.Show("Cette commande est déjà réglée.");
    return;
}
if (commande.IdSuivi == 2 && suivi.Id == 1)
{
    MessageBox.Show("Impossible de revenir en arrière.");
    return;
}
if (suivi.Id == 3 && commande.IdSuivi == 1)
{
    MessageBox.Show("La commande doit être livrée avant d'être réglée.");
    return;
}
CS,
            ],
        ],

        'stack' => [
            'Client'  => ['C#', '.NET Framework 4.7.2', 'WinForms', 'Newtonsoft.Json', 'MSTest', 'SonarLint'],
            'API'     => ['PHP 8.4', 'PDO', 'Composer', 'phpDocumentor'],
            'Données' => ['MySQL', 'MySQL Workbench'],
            'Infra'   => ['VPS OVH', 'Ubuntu', 'nginx', 'PHP-FPM'],
            'Outils'  => ['Visual Studio 2022', 'Postman', 'WinSCP', 'Git'],
        ],

        'documents' => [
            ['ap2-fiche-realisation', 'Fiche descriptive de réalisation', 'AP2 · PDF', null],
            ['ap2-compte-rendu', 'Compte rendu d\'activité', 'Synthèse · PDF', null],
            ['ap2-tests', 'Résultats des tests MSTest', '14 tests · PDF', null],
            ['ap2-cahier-des-charges', 'Cahier des charges', 'Spécifications · PDF', null],
        ],

        'competencies' => $btsCommon,
    ],

    [
        'slug'    => 'mediatek86',
        'title'   => 'Mediatek86',
        'tagline' => 'Le catalogue de formations vidéo d\'un réseau de médiathèques, en ligne et redéployé à chaque push.',
        'summary' => 'Site Symfony en deux parties : un __front public__ pour parcourir les formations et les playlists, un __back-office protégé__ pour tout administrer. Il tourne sur mon VPS, et ==GitHub Actions le met à jour tout seul==.',
        'year'    => 2025,
        'context' => 'Atelier professionnel 1 · BTS SIO',
        'role'    => 'Développement Symfony, tests, déploiement',
        'video'   => 'WEkONaJWiqM',

        'links' => [
            ['Site en ligne', 'https://mediatek.taizsys.xyz/'],
            ['Documentation technique', 'https://docs.mediatek.taizsys.xyz/'],
            ['Dépôt GitHub', 'https://github.com/taizsign/mediatek86'],
        ],

        'architecture' => [
            'nodes' => [
                ['id' => 'dev',     'x' => 30,  'y' => 40,  'w' => 180, 'label' => 'Poste de dev',   'sub' => 'git push',              'kind' => 'client'],
                ['id' => 'github',  'x' => 300, 'y' => 40,  'w' => 150, 'label' => 'GitHub',         'sub' => 'branche main',          'kind' => 'external'],
                ['id' => 'actions', 'x' => 545, 'y' => 40,  'w' => 190, 'label' => 'GitHub Actions', 'sub' => 'workflow deploy.yml',   'kind' => 'external'],
                ['id' => 'visitor', 'x' => 30,  'y' => 270, 'w' => 170, 'label' => 'Visiteur',       'sub' => 'navigateur',            'kind' => 'client'],
                ['id' => 'nginx',   'x' => 330, 'y' => 270, 'w' => 140, 'label' => 'nginx',          'sub' => 'hôte virtuel',          'kind' => 'server'],
                ['id' => 'symfony', 'x' => 560, 'y' => 270, 'w' => 170, 'label' => 'Symfony 6.4',    'sub' => 'PHP-FPM · Doctrine',    'kind' => 'service'],
                ['id' => 'db',      'x' => 820, 'y' => 270, 'w' => 120, 'label' => 'MySQL',          'sub' => 'formations',            'kind' => 'data'],
            ],
            'links' => [
                ['from' => 'dev',     'to' => 'github',  'label' => 'push', 'flow' => true],
                ['from' => 'github',  'to' => 'actions', 'label' => 'déclenche', 'flow' => true],
                ['from' => 'actions', 'to' => 'symfony', 'label' => 'SSH · git pull · composer', 'flow' => true],
                ['from' => 'visitor', 'to' => 'nginx',   'label' => 'HTTPS', 'flow' => true],
                ['from' => 'nginx',   'to' => 'symfony', 'label' => 'FastCGI', 'flow' => true],
                ['from' => 'symfony', 'to' => 'db',      'label' => 'Doctrine', 'flow' => true],
            ],
            'groups' => [
                ['label' => 'VPS OVH · Ubuntu', 'x' => 305, 'y' => 222, 'w' => 645, 'h' => 158],
            ],
        ],
        'architecture_caption' => 'Deux chemins arrivent sur le même serveur : celui des visiteurs, et celui du code. Un push sur main ==déclenche un workflow== qui se connecte en SSH au VPS et met le site à jour, __sans que je touche au serveur__.',

        'brief' => [
            ['L\'existant', 'Le projet Symfony de départ est ==incomplet==. L\'architecture MVC est posée, les entités Doctrine existent, mais une bonne partie des contrôleurs et de la logique métier reste à écrire.'],
            ['La demande', 'Compléter le __site public__ (catalogue, playlists) et ajouter un ==back-office sécurisé== pour gérer les formations, les playlists et les catégories. Puis mettre le tout en ligne.'],
        ],

        'build' => [
            ['Playlists : compter et trier', 'Une colonne affiche le ==nombre de formations== de chaque playlist, triable dans les deux sens. Le même compteur apparaît sur la page de détail.'],
            ['Back-office', 'Ajout, modification et suppression des formations, playlists et catégories, avec les formulaires Symfony. Toute suppression demande ==une confirmation==.'],
            ['Authentification', 'L\'administration est derrière un formulaire de connexion géré par le ==composant Security== de Symfony.'],
            ['Vidéos', 'Chaque formation affiche sa ==vidéo YouTube== à partir de son identifiant.'],
            ['Tests et documentation', '==Tests PHPUnit==, documentation générée avec __phpDocumentor__ et publiée sur son propre sous-domaine.'],
            ['Déploiement continu', 'Un workflow GitHub Actions se connecte en ==SSH au VPS à chaque push== sur main : `git pull`, `composer install` en mode production, vidage du cache, droits rendus à `www-data`.'],
        ],

        'retro' => [
            ['Le tri par nombre de formations', 'Il charge toutes les playlists, compte leurs formations en PHP, puis trie le tableau. Une ==seule requête== avec `COUNT()`, `GROUP BY` et `ORDER BY` ferait le travail __côté base__, sans charger toutes les formations en mémoire.'],
            ['Le déploiement par git pull', 'Le serveur tire directement le dépôt : si `composer install` échoue en cours de route, le site reste dans ==un état bancal==. Je préparerais chaque version dans un dossier à part, puis je basculerais un lien symbolique, avec un __retour arrière en une commande__.'],
            ['Les migrations', 'Le workflow ne lance pas `doctrine:migrations:migrate` : un changement de schéma doit être ==appliqué à la main== en production. Je l\'ajouterais au pipeline, __juste après une sauvegarde__ de la base.'],
            ['Le compte administrateur', 'Il est défini dans `security.yaml`. Pour plusieurs administrateurs, une ==entité User en base== avec des mots de passe hachés est plus propre, et le `.env` versionné doit sortir du dépôt.'],
        ],

        'excerpts' => [
            [
                'title'    => 'Le pipeline de déploiement',
                'language' => 'yaml',
                'source'   => 'mediatek86 · .github/workflows/deploy.yml',
                'caption'  => 'Les accès au serveur (hôte, utilisateur, clé SSH) sont des secrets GitHub, jamais écrits dans le dépôt.',
                'code'     => <<<'YAML'
name: Deploy

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: deploy sur le vps
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ secrets.VPS_HOST }}
          username: ${{ secrets.VPS_USER }}
          key: ${{ secrets.VPS_SSH_KEY }}
          script: |
            cd /var/www/mediatek
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php bin/console cache:clear --env=prod
            chown -R www-data:www-data /var/www/mediatek
YAML,
            ],
            [
                'title'    => 'Trier les playlists par nombre de formations',
                'language' => 'php',
                'source'   => 'mediatek86 · src/Repository/PlaylistRepository.php',
                'caption'  => 'Ça marche, mais le tri se fait en PHP après avoir tout chargé. Voir « Avec du recul » : c\'est un `ORDER BY COUNT(f.id)` qui manque.',
                'code'     => <<<'PHP'
public function findAllOrderByFormationsCount($ordre): array{
    $playlists = $this->findAll();
    $nbrFormations = [];
    foreach($playlists as $p){
        $nbrFormations[] = count($p->getFormations());
    }
    if($ordre == 'ASC'){
        array_multisort($nbrFormations, SORT_ASC, $playlists);
    }else{
        array_multisort($nbrFormations, SORT_DESC, $playlists);
    }
    return $playlists;
}
PHP,
            ],
        ],

        'stack' => [
            'Backend'  => ['PHP 8.4', 'Symfony 6.4', 'Doctrine ORM', 'PHPUnit', 'phpDocumentor'],
            'Frontend' => ['Twig', 'HTML / CSS', 'JavaScript'],
            'Données'  => ['MySQL', 'Doctrine Migrations'],
            'Infra'    => ['VPS OVH', 'Ubuntu', 'nginx', 'PHP-FPM', 'GitHub Actions'],
            'Outils'   => ['Composer', 'Symfony CLI', 'Git'],
        ],

        'documents' => [
            ['ap1-synthese', 'Synthèse de l\'atelier', 'Compte rendu · PDF', 'ap1-synthese.pdf'],
            ['ap1-pv-recette', 'PV de recette', 'Validation finale · PDF', 'ap1-pv-recette.pdf'],
            ['ap1-plan-de-tests', 'Plan de tests', 'Recette et intégration · PDF', 'ap1-plan-de-tests.pdf'],
            ['ap1-cahier-des-charges', 'Cahier des charges', 'Spécifications · PDF', 'ap1-cahier-des-charges.pdf'],
            ['ap1-contrat-developpement', 'Contrat de développement', 'Engagement · PDF', 'ap1-contrat-developpement.pdf'],
            ['ap1-fiche-missions', 'Fiche de missions', 'AP1 · PDF', 'ap1-fiche-missions.pdf'],
        ],

        'competencies' => $btsCommon,
    ],
];

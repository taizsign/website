# Portfolio de Raphaël

PHP 8 orienté objet, sans framework, avec une base SQLite. Aucune dépendance à installer, aucun build.

## Lancer en local

```bash
php -S 127.0.0.1:8765 -t public public/index.php
```

Puis <http://127.0.0.1:8765>. Les bases `storage/portfolio.fr.sqlite` et `storage/portfolio.en.sqlite` se créent toutes seules à la première requête.

Extensions PHP utilisées : `pdo_sqlite`, `curl`, `intl`, `simplexml` (toutes présentes dans une installation standard).

## Architecture

```
public/            ← racine web (seul dossier exposé)
  index.php        ← contrôleur frontal : toutes les requêtes passent ici
  assets/          ← CSS, JS (modules ES, sans bibliothèque), polices auto-hébergées, images
src/
  Kernel.php       ← déclare les routes, appelle le contrôleur, transforme les erreurs en pages
  Container.php    ← crée chaque service à la demande (base, vue, dépôts, sonde…)
  Http/            ← Request, Response (en-têtes de sécurité, CSP), Router (une adresse par langue)
  I18n/            ← Translator : les textes de l'interface en anglais
  Controller/      ← une classe par section du site
  Domain/          ← objets métier immuables : Project, Topology, Milestone…
  Repository/      ← lectures SQL (requêtes préparées uniquement)
  Service/         ← SkillQuery (la console SQL), Uptime (sonde HTTP), FeedReader (RSS)
  Database/        ← Database (PDO), SchemaBuilder, Seeder, ContentTranslation
  View/            ← moteur de gabarits, coloration du code, rendu SVG des schémas
templates/         ← gabarits PHP (layout, pages, partials)
lang/en.php        ← traduction anglaise de l'interface
database/
  schema.sql       ← le schéma : tables, contraintes CHECK, clés étrangères, vues `competence` et `skills`
  content/*.php    ← tout le contenu éditable (projets, compétences, parcours, veille, labo)
  content/en/*.php ← sa traduction anglaise (le texte seulement)
storage/           ← une base SQLite par langue, cache des flux RSS, PDF des projets (hors du dossier public)
bin/console        ← commandes d'administration
```

Le parcours d'une requête : `public/index.php` → `Kernel::handle()` → `Router::match()` → contrôleur → dépôt (SQL) → `View::render()` → `Response::send()`.

## Modifier le contenu

Tout le texte est dans `database/content/`. Modifie un fichier, recharge la page : la base est reconstruite automatiquement (une empreinte du schéma et du contenu est comparée à chaque requête). Les relevés de disponibilité sont conservés.

- `projects.php` : projets, schémas d'architecture, extraits de code, « avec du recul », documents
- `skills.php` : compétences (niveau 0-5, statut `acquis` / `en cours` / `objectif`, preuves)
- `journey.php` : parcours
- `watch.php` : veille
- `lab.php` : la baie du homelab
- `bts.php` : référentiel BTS

Les infos générales (e-mail, réseaux, services surveillés, flux RSS) sont dans `config/app.php`.

## Version anglaise

Le site existe en français (`/`, `/projets`…) et en anglais (`/en`, `/en/projects`…). Les deux drapeaux à côté du logo mènent à la même page dans l'autre langue ; le choix est mémorisé (cookie `lang`), et l'adresse racine ouvre alors directement la version anglaise.

- **Textes de l'interface** : dans les gabarits, le texte est écrit en français et passé à `$this->t('…')`. Sa traduction est dans `lang/en.php`, avec le texte français comme clé. Un texte sans traduction reste en français.
- **Contenu** : `database/content/en/` reprend chaque fichier de contenu, avec le texte seulement (niveaux, liens, code et schémas restent dans le fichier français, qui fait foi). Chaque langue a sa propre base SQLite, reconstruite automatiquement.
- **Console SQL** : en anglais, elle interroge la vue `skills` (colonnes et valeurs en anglais : `status = 'acquired'`). Ce qui s'affiche reste exactement ce qui s'exécute.
- **Adresses** : les chemins anglais sont déclarés à côté des routes françaises dans `Kernel::routes()`.

Après avoir ajouté ou modifié un texte dans un gabarit, `php bin/console i18n:check` liste ceux qui n'ont pas encore de traduction.

## Commandes

```bash
php bin/console db:build                                   # reconstruire les bases (fr et en)
php bin/console db:query "SELECT nom, statut FROM competence"  # requête en lecture
php bin/console db:query "SELECT name, status FROM skills" en  # la même, sur la base anglaise
php bin/console i18n:check                                 # textes de l'interface sans traduction
php bin/console routes                                     # les adresses, dans chaque langue
php bin/console cache:clear                                # vider le cache RSS
```

## Déployer

La racine web doit pointer sur `public/`. `storage/` doit être accessible en écriture par PHP.

**nginx + PHP-FPM**

```nginx
server {
    server_name taizsys.xyz;
    root /var/www/portfolio/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
    }

    location ~ \.php$ { return 404; }

    location ~* \.(css|js|woff2|svg|png)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Apache** : `public/.htaccess` gère la réécriture, le HTTPS et le cache.

## Sécurité

- Requêtes SQL toujours préparées ; la console de compétences ne passe que des valeurs en liste blanche.
- CSP stricte (`script-src 'self'`, aucun script en ligne), en-têtes `nosniff`, `DENY`, `Referrer-Policy`.
- Les PDF sont servis par un contrôleur depuis `storage/documents`, jamais directement.
- Flux RSS et sondes HTTP sont récupérés côté serveur : le navigateur du visiteur ne contacte aucun site tiers (sauf la vidéo YouTube, chargée seulement au clic).

## Crédits

La main de la page d'accueil vient de *La Création d'Adam* de Michel-Ange (1512, domaine public), convertie en trame de points. Polices : Inter Tight et JetBrains Mono (SIL Open Font License).

Logos des compétences (`public/assets/img/logos/`) : [Simple Icons](https://simpleicons.org) (CC0) et [Devicon](https://devicon.dev) (MIT) pour PowerShell, C#, Windows et Azure ; les marques appartiennent à leurs propriétaires. Les icônes réseau, matériel, sécurité, sauvegarde, modélisation, SGBD, API et tests sont dessinées pour le site. Pour en ajouter une, déposer le SVG dans ce dossier et indiquer son nom dans `database/content/skills.php`.

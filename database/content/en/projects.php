<?php

/*
 * Projects in English. Only the text lives here: years, links, code and diagram
 * coordinates come from ../projects.php, which stays the reference.
 *
 *  links     : link labels, in the same order
 *  nodes     : diagram boxes, by id (label, sub)
 *  arrows    : arrow labels, keyed "from>to"
 *  groups    : diagram frames, keyed by their French label
 *  excerpts  : title and caption of each code excerpt, in the same order
 *  layers    : stack layers, French name => English name
 *  technologies : the few technology names that translate
 *  documents : by slug, [label, detail]
 */

return [

    'mediatekdocuments' => [
        'tagline' => 'Library management software that talks to its database through an API I put online myself.',
        'summary' => 'A C# desktop application for library staff: books, DVDs, magazines, orders, subscriptions. It ==never touches the database directly==. Everything goes through a __PHP REST API__ hosted on my VPS.',
        'context' => 'Professional workshop 2 · BTS SIO',
        'role'    => 'C# client, PHP API, MySQL database, deployment',

        'links' => ['Application repository', 'REST API repository'],

        'nodes' => [
            'msi'    => ['label' => '.msi installer', 'sub' => 'deployed on workstations'],
            'nginx'  => ['sub' => 'web server'],
            'api'    => ['label' => 'REST API'],
        ],
        'arrows' => [
            'msi>client' => 'installs',
        ],
        'groups' => [
            'VPS OVH · Ubuntu' => 'OVH VPS · Ubuntu',
            'Poste · Windows'  => 'Workstation · Windows',
        ],
        'architecture_caption' => 'The client has ==no access to MySQL==. It sends HTTP requests (GET, POST, PUT, DELETE) with a JSON body to the API, and only the API talks to the database. Moving the client to another machine or changing the database requires __no change on the client side__.',

        'brief' => [
            ['What was there', 'The starting WinForms application is ==half-built==. You can search and browse books, DVDs and magazines, and record the arrival of a magazine issue. No login, no orders, and a starter REST API that can only handle a few reads.'],
            ['The request', 'Extend the tool for library staff: __book and DVD orders__ with step-by-step tracking, __magazine subscriptions__, an alert before they expire, and different access depending on the department of the logged-in user. All of it as a ==real client-server setup==, with the API deployed on a server.'],
        ],

        'build' => [
            ['Login by department', 'On login, the application fetches the user\'s department and ==adapts what they see==. One department has no access at all, another loses some tabs, and the subscription alert only shows up for the departments concerned.'],
            ['Orders and tracking', 'A book or DVD order moves forward in steps. ==Business rules are checked before anything is sent==: no going back, no paying for an order that hasn\'t been delivered, no more changes once it has been paid.'],
            ['Subscriptions and deadlines', 'On startup, a window lists the subscriptions ending within 30 days. Sorting and date calculations are ==done by MySQL==, not by the client.'],
            ['Completed REST API', 'New PHP routes for users, tracking steps, orders and subscriptions. Every route goes through a ==PDO prepared statement== and answers in __JSON__.'],
            ['Going to production', 'API installed on an ==OVH VPS running Ubuntu== (nginx, PHP-FPM, MySQL), files uploaded with WinSCP, routes tested in Postman. The client ships with a __Windows installer__.'],
            ['Quality', '==14 MSTest unit tests==, __SonarLint__ analysis, a client-side error log in `%AppData%`, and generated technical documentation.'],
        ],

        'retro' => [
            ['Passwords', 'The username and password travel in the URL of a GET request and are ==compared in plain text== against the `utilisateur` table. A URL ends up in the server logs. Today I would send them in a __POST__ and ==hash the password== with `password_hash()` / `password_verify()`.'],
            ['Order numbers', 'The API computes the next id with `MAX(id) + 1`. Two workstations confirming at the same moment can get ==the same number==. An `AUTO_INCREMENT` key settles it.'],
            ['Two writes, no transaction', 'An order is saved with two `INSERT` statements (the `commande` table, then `commandedocument` or `abonnement`). If the second one fails, ==an orphan order is left behind==. Both need to be wrapped in a `BEGIN … COMMIT`.'],
            ['The .env file', 'It is ==committed to the public repository==. Secrets have no place in Git: server environment variables instead, and a `.env.example` in the repository.'],
        ],

        'excerpts' => [
            [
                'title'   => 'Subscriptions expiring within 30 days',
                'caption' => 'A subscription is an order: both tables share the same `id` key, hence the join. All the date logic stays in the database.',
            ],
            [
                'title'   => 'Order tracking rules',
                'caption' => 'Step 1: in progress, 2: delivered, 3: paid. The checks happen before the API is called. In hindsight, the API should check them too, since another client could call it.',
            ],
        ],

        'layers' => ['Données' => 'Data', 'Outils' => 'Tools'],
        'technologies' => ['VPS OVH' => 'OVH VPS'],

        'documents' => [
            'ap2-fiche-realisation'  => ['Project description sheet', 'AP2 · PDF'],
            'ap2-compte-rendu'       => ['Activity report', 'Summary · PDF'],
            'ap2-tests'              => ['MSTest results', '14 tests · PDF'],
            'ap2-cahier-des-charges' => ['Specifications', 'Requirements · PDF'],
        ],
    ],

    'mediatek86' => [
        'tagline' => 'The video training catalogue of a library network, online and redeployed on every push.',
        'summary' => 'A Symfony site in two parts: a __public front end__ to browse trainings and playlists, and a __protected back office__ to manage everything. It runs on my VPS, and ==GitHub Actions keeps it up to date on its own==.',
        'context' => 'Professional workshop 1 · BTS SIO',
        'role'    => 'Symfony development, tests, deployment',

        'links' => ['Live site', 'Technical documentation', 'GitHub repository'],

        'nodes' => [
            'dev'     => ['label' => 'Dev machine'],
            'github'  => ['sub' => 'main branch'],
            'visitor' => ['label' => 'Visitor', 'sub' => 'browser'],
            'nginx'   => ['sub' => 'virtual host'],
            'db'      => ['sub' => 'trainings'],
        ],
        'arrows' => [
            'github>actions' => 'triggers',
        ],
        'groups' => [
            'VPS OVH · Ubuntu' => 'OVH VPS · Ubuntu',
        ],
        'architecture_caption' => 'Two paths lead to the same server: the visitors\' path and the code\'s path. A push to main ==triggers a workflow== that connects to the VPS over SSH and updates the site, __without me touching the server__.',

        'brief' => [
            ['What was there', 'The starting Symfony project is ==unfinished==. The MVC architecture is in place and the Doctrine entities exist, but a good part of the controllers and business logic is still to be written.'],
            ['The request', 'Complete the __public site__ (catalogue, playlists) and add a ==secure back office== to manage trainings, playlists and categories. Then put the whole thing online.'],
        ],

        'build' => [
            ['Playlists: count and sort', 'A column shows ==how many trainings== each playlist holds, sortable both ways. The same count appears on the detail page.'],
            ['Back office', 'Adding, editing and deleting trainings, playlists and categories, with Symfony forms. Every deletion asks for ==confirmation==.'],
            ['Authentication', 'The admin area sits behind a login form handled by the ==Symfony Security component==.'],
            ['Videos', 'Each training shows its ==YouTube video== from its id.'],
            ['Tests and documentation', '==PHPUnit tests==, documentation generated with __phpDocumentor__ and published on its own subdomain.'],
            ['Continuous deployment', 'A GitHub Actions workflow connects to the ==VPS over SSH on every push== to main: `git pull`, `composer install` in production mode, cache cleared, ownership handed back to `www-data`.'],
        ],

        'retro' => [
            ['Sorting by number of trainings', 'It loads every playlist, counts their trainings in PHP, then sorts the array. A ==single query== with `COUNT()`, `GROUP BY` and `ORDER BY` would do the work __in the database__, without loading every training into memory.'],
            ['Deploying with git pull', 'The server pulls the repository directly: if `composer install` fails halfway, the site is left in ==a broken state==. I would build each release in its own folder, then switch a symbolic link, with a __one-command rollback__.'],
            ['Migrations', 'The workflow does not run `doctrine:migrations:migrate`, so a schema change has to be ==applied by hand== in production. I would add it to the pipeline, __right after a database backup__.'],
            ['The admin account', 'It is defined in `security.yaml`. For several administrators, a ==User entity in the database== with hashed passwords is cleaner, and the committed `.env` has to leave the repository.'],
        ],

        'excerpts' => [
            [
                'title'   => 'The deployment pipeline',
                'caption' => 'Server access (host, user, SSH key) lives in GitHub secrets, never written in the repository.',
            ],
            [
                'title'   => 'Sorting playlists by number of trainings',
                'caption' => 'It works, but the sorting happens in PHP after loading everything. See "In hindsight": an `ORDER BY COUNT(f.id)` is what\'s missing.',
            ],
        ],

        'layers' => ['Données' => 'Data', 'Outils' => 'Tools'],
        'technologies' => ['VPS OVH' => 'OVH VPS'],

        'documents' => [
            'ap1-synthese'              => ['Workshop summary', 'Report · PDF'],
            'ap1-pv-recette'            => ['Acceptance report', 'Final sign-off · PDF'],
            'ap1-plan-de-tests'         => ['Test plan', 'Acceptance and integration · PDF'],
            'ap1-cahier-des-charges'    => ['Specifications', 'Requirements · PDF'],
            'ap1-contrat-developpement' => ['Development contract', 'Commitment · PDF'],
            'ap1-fiche-missions'        => ['Assignment sheet', 'AP1 · PDF'],
        ],
    ],
];

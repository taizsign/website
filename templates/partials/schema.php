<?php
/**
 * Données structurées (schema.org) de l'accueil, lues par Google :
 * qui je suis, le nom du site et mes autres profils.
 *
 * @var App\View\View $this
 * @var array<string, mixed> $site
 */
$root = rtrim($site['url'], '/');
$home = $root . $this->url('home');

$schema = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'      => 'WebSite',
            '@id'        => $root . '/#website',
            'url'        => $root . '/',
            'name'       => $site['name'],
            'inLanguage' => $this->locale(),
            'publisher'  => ['@id' => $root . '/#person'],
        ],
        [
            '@type'       => 'ProfilePage',
            '@id'         => $home . '#page',
            'url'         => $home,
            'name'        => $this->t($site['title']),
            'description' => $this->t($site['description']),
            'inLanguage'  => $this->locale(),
            'isPartOf'    => ['@id' => $root . '/#website'],
            'mainEntity'  => ['@id' => $root . '/#person'],
        ],
        [
            '@type'       => 'Person',
            '@id'         => $root . '/#person',
            'name'        => $site['name'],
            'url'         => $home,
            'image'       => $root . '/assets/img/og.png',
            'jobTitle'    => $this->t('Étudiant BTS SIO SLAM'),
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => strtok($site['location'], ','),
                'addressCountry'  => 'FR',
            ],
            'knowsAbout'  => ['Linux', 'nginx', 'MySQL', 'PHP', 'Symfony', 'C#', 'PowerShell', 'Git'],
            'sameAs'      => [$site['github']],
        ],
    ],
];
?>
<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR) ?></script>

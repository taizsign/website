<?php
/**
 * Gabarit commun à toutes les pages.
 *
 * @var App\View\View $this
 * @var array<string, mixed> $site
 * @var string $content
 * @var ?string $title
 */
$pageTitle = isset($title) && $title !== null ? $title . ' · ' . $site['name'] : $this->t($site['title']);
$pageDescription = $description ?? $this->t($site['description']);
$route = $this->get('route', '');
$locale = $this->locale();
$alternates = $this->alternates();
?>
<!doctype html>
<html lang="<?= $this->e($locale) ?>" data-route="<?= $this->e($route) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= $this->e($pageTitle) ?></title>
    <meta name="description" content="<?= $this->e($pageDescription) ?>">
    <meta name="author" content="<?= $this->e($site['name']) ?>">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $this->e($pageTitle) ?>">
    <meta property="og:description" content="<?= $this->e($pageDescription) ?>">
    <meta property="og:locale" content="<?= $this->e($alternates[$locale]['tag']) ?>">
    <?php if ($route !== 'error'): ?>
        <?php foreach ($alternates as $code => $alternate): ?>
            <link rel="alternate" hreflang="<?= $this->e($code) ?>" href="<?= $this->e($site['url'] . $alternate['url']) ?>">
        <?php endforeach; ?>
        <link rel="alternate" hreflang="x-default" href="<?= $this->e($site['url'] . reset($alternates)['url']) ?>">
    <?php endif; ?>
    <link rel="icon" href="<?= $this->asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preload" href="/assets/fonts/inter-tight-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/assets/fonts/jetbrains-mono-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= $this->asset('css/app.css') ?>">
    <script src="<?= $this->asset('js/boot.js') ?>"></script>
    <script type="module" src="<?= $this->asset('js/app.js') ?>"></script>
</head>
<body id="top">
    <a class="skip" href="#main"><?= $this->t('Aller au contenu') ?></a>
    <canvas class="field" id="field" aria-hidden="true"></canvas>
    <div class="progress" aria-hidden="true" data-progress><span class="progress__fill"></span><span class="progress__dot"></span></div>

    <?= $this->partial('partials/header') ?>

    <main id="main">
        <?= $content ?>
    </main>

    <?= $this->partial('partials/footer') ?>

    <div class="nightlight-layer" aria-hidden="true" data-nightlight-layer></div>
</body>
</html>

<?php
/** @var App\View\View $this */
$links = [
    ['projects', $this->t('Projets'), 'project'],
    ['skills', $this->t('Compétences'), 'skills'],
    ['journey', $this->t('Parcours'), 'journey'],
    ['watch', $this->t('Veille'), 'watch'],
    ['lab', $this->t('Labo'), 'lab'],
];
$isHome = $this->isCurrent('home');
?>
<header class="bar<?= $isHome ? ' bar--over' : '' ?>" data-bar>
    <div class="bar__inner">
        <div class="brand">
            <!-- L'icône est un interrupteur : filtre de lumière bleue (teinte chaude sur tout le site) -->
            <button class="nightlight" type="button" aria-pressed="false" aria-label="<?= $this->t('Activer le filtre de lumière bleue') ?>" data-nightlight
                    data-label-on="<?= $this->t('Désactiver le filtre de lumière bleue') ?>" data-label-off="<?= $this->t('Activer le filtre de lumière bleue') ?>"
                    data-tip-on="<?= $this->t('Filtre activé, cliquer pour couper') ?>" data-tip-off="<?= $this->t('Filtre lumière bleue') ?>">
                <svg viewBox="0 0 28 28" fill="none" stroke-width="2.2" aria-hidden="true">
                    <rect class="nightlight__frame" x="2" y="2" width="24" height="24" rx="7" pathLength="1"/>
                    <path class="nightlight__chevron" d="M8.5 10l4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path class="nightlight__cursor" d="M15 18h5" stroke-linecap="round"/>
                </svg>
                <span class="nightlight__tip" aria-hidden="true" data-nightlight-tip><?= $this->t('Filtre lumière bleue') ?></span>
            </button>
            <a class="logo" href="<?= $this->url('home') ?>" aria-label="<?= $this->t('Accueil') ?>">raphaël</a>
            <?= $this->partial('partials/lang') ?>
        </div>

        <nav class="nav" aria-label="<?= $this->t('Navigation principale') ?>">
            <?php foreach ($links as [$route, $label, $prefix]): ?>
                <a href="<?= $this->url($route) ?>"<?= $this->isCurrent($prefix) ? ' aria-current="page"' : '' ?>><?= $label ?></a>
            <?php endforeach; ?>
        </nav>

        <a class="btn btn--cta bar__cta" href="#contact">
            <?= $this->t('Me contacter') ?>
            <span class="arrow" aria-hidden="true"><?= $this->partial('partials/arrow') ?><?= $this->partial('partials/arrow') ?></span>
        </a>

        <button class="burger" type="button" aria-expanded="false" aria-controls="menu" data-burger>
            <span class="burger__lines" aria-hidden="true"></span>
            <span class="sr-only"><?= $this->t('Menu') ?></span>
        </button>
    </div>
</header>

<div class="menu" id="menu" hidden data-menu>
    <nav class="menu__nav" aria-label="<?= $this->t('Navigation mobile') ?>">
        <?php foreach ($links as $i => [$route, $label, $prefix]): ?>
            <a href="<?= $this->url($route) ?>"<?= $this->isCurrent($prefix) ? ' aria-current="page"' : '' ?>>
                <span class="menu__num">0<?= $i + 1 ?></span><?= $label ?>
            </a>
        <?php endforeach; ?>
        <a href="#contact" data-menu-close><span class="menu__num">→</span><?= $this->t('Me contacter') ?></a>
    </nav>
</div>

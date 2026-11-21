<?php
/**
 * @var App\View\View $this
 * @var array<string, mixed> $site
 * @var list<App\Domain\Project> $projects
 */
?>
<section class="page-head">
    <div class="wrap">
        <p class="crumb" data-reveal><a href="<?= $this->url('home') ?>">~</a> / <?= $this->t('projets') ?></p>
        <h1 class="h1" data-reveal><?= $this->t('Projets') ?></h1>
        <p class="lead lead--wide" data-reveal><?= $this->t("Deux applications complètes, construites pendant le BTS. Pour chacune&nbsp;: le contexte, l'architecture, du vrai code tiré des dépôts, et ce que je referais autrement aujourd'hui.") ?></p>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap">
        <?= $this->partial('partials/project-list', ['projects' => $projects]) ?>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap">
        <?= $this->rule($this->t('Aussi sur GitHub'), 'github.com/taizsign') ?>
        <ul class="rows" data-reveal>
            <li class="row">
                <a class="row__link" href="https://github.com/taizsign/setup-computer" target="_blank" rel="noopener">
                    <span class="row__name">setup-computer</span>
                    <span class="row__desc"><?= $this->t('Script PowerShell qui prépare un poste Windows en une commande. Détaillé dans le <span class="u">Labo</span>.') ?></span>
                    <span class="row__meta">PowerShell ↗</span>
                </a>
            </li>
            <li class="row">
                <a class="row__link" href="https://github.com/taizsign/mesvoyages" target="_blank" rel="noopener">
                    <span class="row__name">mesvoyages</span>
                    <span class="row__desc"><?= $this->t('Application Symfony&nbsp;: catalogue de visites, formulaire de contact, authentification et jeux de données de test.') ?></span>
                    <span class="row__meta">PHP ↗</span>
                </a>
            </li>
        </ul>
    </div>
</section>

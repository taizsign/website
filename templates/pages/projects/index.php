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
        <h1 class="h1" data-reveal><?= $this->t('Projets') ?><span class="title-semi" aria-hidden="true">;</span></h1>
        <p class="lead lead--wide" data-reveal><?= $this->t("<em>Deux applications complètes</em> construites pendant le BTS, et <em>ce site</em>. Pour chacun, le <em>contexte</em>, l'<em>architecture</em>, des extraits du <em>code source</em> et ce que je <em>referais autrement</em> aujourd'hui.") ?></p>

        <?php
        // chaque technologie, avec le nombre de projets qui l'utilisent
        $techs = [];
        foreach ($projects as $project) {
            foreach ($this->techList($project) as $tech) {
                $techs[$tech] = ($techs[$tech] ?? 0) + 1;
            }
        }
        uksort($techs, static fn (string $a, string $b): int => [$techs[$b], strtolower($a)] <=> [$techs[$a], strtolower($b)]);
        ?>
        <div class="techsearch" data-techsearch data-reveal
             data-one="<?= $this->t('{n} projet utilise {tech}') ?>" data-many="<?= $this->t('{n} projets utilisent {tech}') ?>">
            <div class="techsearch__field">
                <svg class="techsearch__icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></svg>
                <input class="techsearch__input" id="techsearch" type="text" role="combobox" autocomplete="off" spellcheck="false"
                       placeholder="<?= $this->t('Filtrer par langage ou technologie') ?>" aria-label="<?= $this->t('Filtrer les projets par technologie') ?>"
                       aria-expanded="false" aria-controls="techsearch-list" aria-autocomplete="list">
                <button class="techsearch__clear" type="button" aria-label="<?= $this->t('Effacer le filtre') ?>" hidden>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <ul class="techsearch__list" id="techsearch-list" role="listbox" hidden>
                <?php foreach ($techs as $tech => $count): ?>
                    <li class="techsearch__option" role="option" aria-selected="false" data-tech="<?= $this->e($tech) ?>">
                        <img src="<?= $this->techLogo($tech) ?>" alt="" width="18" height="18" loading="lazy">
                        <span class="techsearch__name"><?= $this->e($tech) ?></span>
                        <span class="techsearch__count"><?= $this->plural($count, 'projet') ?></span>
                    </li>
                <?php endforeach; ?>
                <li class="techsearch__empty" hidden><?= $this->t('Aucune technologie ne correspond.') ?></li>
            </ul>
            <p class="techsearch__status" aria-live="polite"></p>
        </div>
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

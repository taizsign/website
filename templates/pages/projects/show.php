<?php
/**
 * @var App\View\View $this
 * @var App\Domain\Project $project
 * @var ?App\Domain\Project $next
 */
$available = count(array_filter($project->documents, static fn ($d): bool => $d->isAvailable()));
$pending = count($project->documents) - $available;
$docsMeta = implode(' · ', array_filter([
    $available > 0 ? $available . "\u{00A0}PDF" : null,
    $pending > 0 ? $pending . "\u{00A0}" . $this->t('à venir') : null,
]));
?>
<article class="case">

    <header class="page-head case__head">
        <div class="wrap">
            <p class="crumb" data-reveal><a href="<?= $this->url('home') ?>">~</a> / <a href="<?= $this->url('projects') ?>"><?= $this->t('projets') ?></a> / <?= $project->number() ?></p>
            <h1 class="h1 case__title" data-reveal><?= $this->breakable($project->title) ?></h1>
            <p class="lead lead--wide" data-reveal><?= $this->e($project->tagline) ?></p>

            <dl class="facts" data-reveal>
                <div><dt><?= $this->t('Année') ?></dt><dd><?= $project->year ?></dd></div>
                <div><dt><?= $this->t('Cadre') ?></dt><dd><?= $this->e($project->context) ?></dd></div>
                <div><dt><?= $this->t("Ce que j'ai fait") ?></dt><dd><?= $this->e($project->role) ?></dd></div>
                <div>
                    <dt><?= $this->t('Liens') ?></dt>
                    <dd class="facts__links">
                        <?php foreach ($project->links as $link): ?>
                            <a href="<?= $this->e($link->url) ?>" target="_blank" rel="noopener"><?= $this->e($link->label) ?> ↗</a>
                        <?php endforeach; ?>
                    </dd>
                </div>
            </dl>
        </div>
    </header>

    <!-- Architecture -->
    <section class="section section--arch">
        <div class="wrap">
            <?= $this->rule($this->t('Architecture'), $this->plural(count($project->architecture->nodes), 'composant')) ?>
            <figure class="arch" data-reveal>
                <?= $this->topology($project->architecture, 'topo-case') ?>
                <figcaption><?= $this->inline($project->architectureCaption) ?></figcaption>
            </figure>
            <p class="arch__hint" aria-hidden="true"><?= $this->t('Survolez un élément pour voir ses liaisons.') ?></p>
        </div>
    </section>

    <!-- Brief -->
    <section class="section">
        <div class="wrap split">
            <div class="split__side">
                <?= $this->rule($this->t('Le brief'), $project->context) ?>
                <p class="split__summary" data-reveal><?= $this->e($project->summary) ?></p>
            </div>
            <div class="split__main brief">
                <?php foreach ($project->sections('brief') as $section): ?>
                    <div class="brief__item" data-reveal>
                        <h2 class="h3"><?= $this->e($section->title) ?></h2>
                        <?= $this->text($section->body) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Réalisation -->
    <section class="section">
        <div class="wrap">
            <div class="section__head">
                <?= $this->rule($this->t("Ce que j'ai construit"), $this->plural(count($project->sections('build')), 'réalisation')) ?>
            </div>
            <ol class="builds">
                <?php foreach ($project->sections('build') as $i => $section): ?>
                    <li class="build" data-reveal>
                        <span class="build__num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <h3 class="build__title"><?= $this->e($section->title) ?></h3>
                        <div class="build__body"><?= $this->text($section->body) ?></div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- Extraits de code -->
    <?php if ($project->excerpts !== []): ?>
        <section class="section">
            <div class="wrap">
                <div class="section__head">
                    <?= $this->rule($this->t('Sous le capot'), $this->plural(count($project->excerpts), 'extrait')) ?>
                    <p class="lead" data-reveal><?= $this->t('Du code copié tel quel depuis les dépôts, sans retouche.') ?></p>
                </div>
                <div class="excerpts">
                    <?php foreach ($project->excerpts as $excerpt): ?>
                        <figure class="excerpt" data-reveal>
                            <div class="excerpt__bar">
                                <span class="excerpt__title"><?= $this->e($excerpt->title) ?></span>
                                <span class="excerpt__lang"><?= $this->e($excerpt->language) ?></span>
                            </div>
                            <pre class="code" tabindex="0"><code class="lang-<?= $this->e($excerpt->language) ?>"><?= $this->code($excerpt) ?></code></pre>
                            <figcaption>
                                <span class="excerpt__source"><?= $this->e($excerpt->source) ?></span>
                                <span><?= $this->inline($excerpt->caption) ?></span>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Avec du recul -->
    <?php if ($project->sections('retro') !== []): ?>
        <section class="section section--dark">
            <div class="wrap">
                <div class="section__head">
                    <?= $this->rule($this->t('Avec du recul'), $this->plural(count($project->sections('retro')), 'point'), true) ?>
                    <h2 class="h2" data-reveal><?= $this->t('Ce que je<br>referais autrement.') ?></h2>
                    <p class="lead" data-reveal><?= $this->t("Relu aujourd'hui avec un œil plus systèmes et bases de données. Rien de tout ça n'empêchait le projet de fonctionner&nbsp;; tout ça compterait en production.") ?></p>
                </div>
                <ol class="retros">
                    <?php foreach ($project->sections('retro') as $i => $section): ?>
                        <li class="retro" data-reveal>
                            <span class="retro__tag">#<?= $i + 1 ?></span>
                            <h3 class="retro__title"><?= $this->e($section->title) ?></h3>
                            <div class="retro__body"><?= $this->text($section->body) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>
    <?php endif; ?>

    <!-- Stack -->
    <section class="section">
        <div class="wrap split">
            <div class="split__side">
                <?= $this->rule($this->t('Stack'), $this->plural(array_sum(array_map('count', $project->stack)), 'technologie')) ?>
            </div>
            <div class="split__main">
                <dl class="stack" data-reveal>
                    <?php foreach ($project->stack as $layer => $technologies): ?>
                        <div class="stack__row">
                            <dt><?= $this->e($layer) ?></dt>
                            <dd><ul class="chips"><?php foreach ($technologies as $tech): ?><li><?= $this->e($tech) ?></li><?php endforeach; ?></ul></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        </div>
    </section>

    <!-- Démonstration -->
    <?php if ($project->videoId !== null): ?>
        <section class="section">
            <div class="wrap split">
                <div class="split__side">
                    <?= $this->rule($this->t('Démonstration'), $this->t('vidéo')) ?>
                    <p class="split__summary" data-reveal><?= $this->t("La vidéo ne se charge qu'au clic&nbsp;: pas de cookie YouTube tant que vous ne l'avez pas demandée.") ?></p>
                </div>
                <div class="split__main">
                    <div class="video" data-video="<?= $this->e($project->videoId) ?>" data-reveal>
                        <button class="video__play" type="button" aria-label="<?= $this->t('Lire la démonstration de {title}', ['title' => $project->title]) ?>">
                            <span class="video__icon" aria-hidden="true"></span>
                            <span><?= $this->t('Lire la démonstration') ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Documents -->
    <?php if ($project->documents !== []): ?>
        <section class="section">
            <div class="wrap split">
                <div class="split__side">
                    <?= $this->rule($this->t('Documents'), $docsMeta) ?>
                </div>
                <div class="split__main">
                    <ul class="rows" data-reveal>
                        <?php foreach ($project->documents as $doc): ?>
                            <li class="row<?= $doc->isAvailable() ? '' : ' is-pending' ?>">
                                <?php if ($doc->isAvailable()): ?>
                                    <a class="row__link" href="<?= $this->url('document', ['slug' => $doc->slug]) ?>" target="_blank" rel="noopener">
                                        <span class="row__name"><?= $this->e($doc->label) ?></span>
                                        <span class="row__meta"><?= $this->e($doc->meta) ?> ↗</span>
                                    </a>
                                <?php else: ?>
                                    <span class="row__link">
                                        <span class="row__name"><?= $this->e($doc->label) ?></span>
                                        <span class="row__meta"><?= $this->e($doc->meta) ?> · <?= $this->t('à venir') ?></span>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Compétences BTS -->
    <?php if ($project->competencies !== []): ?>
        <section class="section">
            <div class="wrap split">
                <div class="split__side">
                    <?= $this->rule($this->t('Référentiel BTS SIO'), $this->plural(array_sum(array_map(static fn (array $b): int => count($b['items']), $project->competencies)), 'compétence')) ?>
                    <p class="split__summary" data-reveal><?= $this->t('Les compétences du référentiel mobilisées par ce projet.') ?></p>
                </div>
                <div class="split__main bts">
                    <?php foreach ($project->competencies as $code => $block): ?>
                        <div class="bts__block" data-reveal>
                            <h3 class="bts__title"><span><?= $this->e($code) ?></span><?= $this->e($block['label']) ?></h3>
                            <ul class="bts__list">
                                <?php foreach ($block['items'] as $itemCode => $itemLabel): ?>
                                    <li><code><?= $this->e($itemCode) ?></code><?= $this->e($itemLabel) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Projet suivant -->
    <?php if ($next !== null): ?>
        <nav class="next" aria-label="<?= $this->t('Projet suivant') ?>">
            <a class="next__link" href="<?= $this->url('project', ['slug' => $next->slug]) ?>">
                <span class="next__label"><?= $this->t('Projet suivant') ?> · <?= $next->number() ?></span>
                <span class="next__title"><span><?= $this->breakable($next->title) ?></span> <span aria-hidden="true">→</span></span>
            </a>
        </nav>
    <?php endif; ?>
</article>

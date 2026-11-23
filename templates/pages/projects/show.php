<?php
/**
 * @var App\View\View $this
 * @var App\Domain\Project $project
 * @var ?App\Domain\Project $next
 */
$available = count(array_filter($project->documents, static fn ($d): bool => $d->isAvailable()));
$pending = count($project->documents) - $available;
$docsMeta = implode(', ', array_filter([
    $available > 0 ? $available . "\u{00A0}PDF" : null,
    $pending > 0 ? $pending . "\u{00A0}" . $this->t('à venir') : null,
]));
?>
<article class="case">

    <header class="page-head case__head">
        <div class="wrap">
            <p class="crumb" data-reveal><a href="<?= $this->url('home') ?>">~</a> / <a href="<?= $this->url('projects') ?>"><?= $this->t('projets') ?></a> / <?= $project->number() ?></p>
            <p class="case__kicker" data-reveal><?= $this->e(explode(' / ', $project->context)[0]) ?>, <?= $project->year ?></p>
            <?php // la dernière lettre du nom arrive en sautant au chargement de la page ?>
            <h1 class="h1 case__title" data-reveal aria-label="<?= $this->e($project->title) ?>"><span aria-hidden="true"><?= $this->breakable(mb_substr($project->title, 0, -1)) ?><span class="case__letter"><?= $this->e(mb_substr($project->title, -1)) ?></span></span></h1>
            <p class="lead lead--wide" data-reveal><?= $this->e($project->tagline) ?></p>

            <div class="case__bar" data-reveal>
                <p class="case__role"><?= $this->e($project->role) ?></p>
                <div class="case__links">
                    <?php foreach ($project->links as $i => $link): ?>
                        <?php
                        $isRepo = str_contains($link->url, 'github.com');
                        $isDoc = !$isRepo && (str_contains($link->url, 'doc') || stripos($link->label, 'doc') !== false);
                        ?>
                        <a class="btn<?= $i > 0 ? ' btn--ghost' : '' ?> case__link" href="<?= $this->e($link->url) ?>" target="_blank" rel="noopener">
                            <?php if ($isRepo): ?>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                            <?php elseif ($isDoc): ?>
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="is-stroke"><path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 12h6M9 16h6"/></svg>
                            <?php else: ?>
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="is-stroke"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/></svg>
                            <?php endif; ?>
                            <?= $this->e($link->label) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Architecture -->
    <section class="section section--arch">
        <div class="wrap">
            <?= $this->rule($this->t('Architecture'), $this->plural(count($project->architecture->nodes), 'composant')) ?>
            <figure class="arch" data-reveal>
                <figcaption><?= $this->inline($project->architectureCaption) ?></figcaption>
                <?= $this->topology($project->architecture, 'topo-case') ?>
            </figure>
            <p class="arch__hint" aria-hidden="true"><?= $this->t('Survolez un élément pour voir ses liaisons.') ?></p>
        </div>
    </section>

    <!-- Brief -->
    <section class="section">
        <div class="wrap split">
            <div class="split__side">
                <?= $this->rule($this->t('Le contexte')) ?>
                <p class="split__summary" data-reveal><?= $this->inline($project->summary) ?></p>
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
            <ol class="thread">
                <?php foreach ($project->sections('build') as $section): ?>
                    <li class="thread__item" data-reveal>
                        <h3 class="thread__title"><?= $this->e($section->title) ?></h3>
                        <div class="thread__body"><?= $this->text($section->body) ?></div>
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
                    <?= $this->rule($this->t('Extraits de code'), $this->plural(count($project->excerpts), 'extrait')) ?>
                    <p class="lead" data-reveal><?= $this->t('Du code copié tel quel depuis les dépôts&nbsp;:') ?></p>
                </div>
                <div class="excerpts">
                    <?php foreach ($project->excerpts as $excerpt): ?>
                        <figure class="excerpt" data-reveal>
                            <div class="excerpt__bar">
                                <span class="excerpt__title"><?= $this->e($excerpt->title) ?></span>
                                <span class="excerpt__lang"><?= $this->e(['csharp' => 'C#', 'sql' => 'SQL', 'php' => 'PHP', 'yaml' => 'YAML', 'js' => 'JavaScript', 'javascript' => 'JavaScript', 'bash' => 'Bash', 'powershell' => 'PowerShell'][$excerpt->language] ?? $excerpt->language) ?></span>
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
        <div class="wrap">
            <div class="section__head">
                <?= $this->rule($this->t('Stack'), $this->plural(array_sum(array_map('count', $project->stack)), 'technologie')) ?>
            </div>
            <?php // une colonne par couche, les technologies empilées dessous comme une fiche technique ?>
            <div class="stack" data-reveal>
                <?php foreach ($project->stack as $layer => $technologies): ?>
                    <section class="stack__col">
                        <h3 class="stack__layer"><span><?= $this->e($layer) ?></span></h3>
                        <ul class="stack__list">
                            <?php foreach ($technologies as $tech): ?>
                                <li><img src="<?= $this->techLogo($tech) ?>" alt="" width="18" height="18" loading="lazy"><?= $this->e($tech) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endforeach; ?>
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
                                        <span class="row__meta"><?= $this->e($doc->meta) ?>, <?= $this->t('à venir') ?></span>
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
            <div class="wrap">
                <div class="section__head">
                    <?= $this->rule($this->t('Référentiel BTS SIO'), $this->plural(array_sum(array_map(static fn (array $b): int => count($b['items']), $project->competencies)), 'compétence')) ?>
                    <p class="lead" data-reveal><?= $this->t('Les compétences du référentiel mobilisées par ce projet.') ?></p>
                </div>
                <div class="bts">
                    <?php foreach ($project->competencies as $code => $block): ?>
                        <div class="bts__block" data-reveal>
                            <h3 class="bts__title"><span><?= $this->e($code) ?></span><span class="bts__label"><?= $this->e($block['label']) ?></span></h3>
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
                <span class="next__label"><?= $this->t('Projet suivant') ?> / <?= $next->number() ?></span>
                <span class="next__title"><span><?= $this->breakable($next->title) ?></span> <span aria-hidden="true">→</span></span>
            </a>
        </nav>
    <?php endif; ?>
</article>

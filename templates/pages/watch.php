<?php
/**
 * @var App\View\View $this
 * @var array<string, list<App\Domain\WatchItem>> $watch
 * @var list<array{name: string, url: string, items: list<array{title: string, url: string, date: ?int}>}> $feeds
 */
$formatter = new IntlDateFormatter($this->locale() === 'fr' ? 'fr_FR' : 'en_GB', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE, 'Europe/Paris', pattern: 'd MMM');
$intro = $watch['intro'] ?? [];
$eventYears = array_map(static fn ($e): string => $e->eventParts()['year'], $watch['event'] ?? []);
$years = $eventYears === [] ? null : reset($eventYears) . ' → ' . end($eventYears);
?>
<section class="page-head">
    <div class="wrap">
        <p class="crumb" data-reveal><a href="<?= $this->url('home') ?>">~</a> / <?= $this->t('veille') ?></p>
        <?= $this->rule($this->t('Veille technologique'), $this->t('édition 2026')) ?>
        <h1 class="h1 h1--long" data-reveal><?= $this->t("L'IA générative dans le développement logiciel") ?><span class="title-semi" aria-hidden="true">;</span></h1>
        <p class="lead lead--wide" data-reveal><?= $this->t("De l'assistant de code à l'agent autonome.") ?></p>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap">
        <?php if (isset($intro[0])): ?>
            <blockquote class="question" data-reveal>
                <?= $this->rule($this->t('Problématique'), null, false, false) ?>
                <p class="question__text"><?= $this->inline($intro[0]->body) ?></p>
            </blockquote>
        <?php endif; ?>

        <?php if (isset($intro[1])): ?>
            <div class="split sub">
                <div class="split__side">
                    <?= $this->rule($this->t('Sous-problématique')) ?>
                    <p class="split__summary" data-reveal><?= $this->e($intro[1]->title) ?></p>
                </div>
                <div class="split__main" data-reveal>
                    <?= $this->text($intro[1]->body) ?>
                    <?php if ($intro[1]->url !== null): ?>
                        <p><a class="link" href="<?= $this->e($intro[1]->url) ?>" target="_blank" rel="noopener"><?= $this->e($intro[1]->meta) ?> ↗</a></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section__head">
            <?= $this->rule($this->t('Axes de recherche'), $this->plural(count($watch['axis'] ?? []), 'axe')) ?>
        </div>
        <ol class="axes">
            <?php foreach ($watch['axis'] ?? [] as $i => $axis): ?>
                <li class="axis" data-reveal>
                    <span class="axis__num">0<?= $i + 1 ?></span>
                    <h2 class="h3"><?= $this->e($axis->title) ?></h2>
                    <p><?= $this->inline($axis->body) ?></p>
                    <p class="axis__tags"><?= $this->e($axis->meta) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section section--timeline">
    <div class="wrap">
        <div class="section__head">
            <?= $this->rule($this->t('Chronologie'), $years) ?>
            <p class="lead" data-reveal><?= $this->t('Faites défiler horizontalement.') ?></p>
        </div>
    </div>
    <ol class="chrono" tabindex="0" aria-label="<?= $this->t('Chronologie') ?>" data-chrono>
        <?php foreach ($watch['event'] ?? [] as $event): $parts = $event->eventParts(); ?>
            <li class="chrono__event<?= $parts['tag'] === $this->t('EN COURS') ? ' is-now' : '' ?>">
                <time class="chrono__when"><span><?= $this->e($parts['year']) ?></span><?= $this->e(mb_strtolower($parts['month'])) ?></time>
                <span class="chrono__dot" aria-hidden="true"></span>
                <span class="chrono__tag"><?= $this->e($parts['tag']) ?></span>
                <h3 class="chrono__title"><?= $this->e($event->title) ?></h3>
                <p><?= $this->inline($event->body) ?></p>
            </li>
        <?php endforeach; ?>
        <li class="chrono__end" aria-hidden="true"><?= $this->t('à suivre…') ?></li>
    </ol>
</section>

<section class="section">
    <div class="wrap">
        <div class="section__head">
            <?= $this->rule($this->t('Synthèse'), $this->plural(count($watch['insight'] ?? []), 'enseignement')) ?>
        </div>
        <ol class="insights">
            <?php foreach ($watch['insight'] ?? [] as $i => $insight): ?>
                <li class="insight" data-reveal>
                    <span class="insight__num"><?= $i + 1 ?></span>
                    <div>
                        <h2 class="h3"><?= $this->e($insight->title) ?></h2>
                        <p><?= $this->inline($insight->body) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section__head">
            <?= $this->rule($this->t('Le fil, en direct'), $this->t('actualisé toutes les heures')) ?>
            <p class="lead" data-reveal><?= $this->t('Les derniers articles de mes flux, récupérés par le serveur et mis en cache une heure. Votre navigateur ne contacte aucun de ces sites.') ?></p>
        </div>
        <div class="feeds">
            <?php foreach ($feeds as $feed): ?>
                <section class="feed" lang="<?= $this->e($feed['lang']) ?>" data-reveal>
                    <h3 class="feed__name"><?= $this->e($feed['name']) ?></h3>
                    <?php if ($feed['items'] === []): ?>
                        <p class="feed__empty"><?= $this->t('Flux indisponible pour le moment.') ?></p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($feed['items'] as $item): ?>
                                <li>
                                    <a href="<?= $this->e($item['url']) ?>" target="_blank" rel="noopener noreferrer">
                                        <?php if ($item['date'] !== null): ?><time><?= $this->e($formatter->format($item['date'])) ?></time><?php endif; ?>
                                        <span><?= $this->e($item['title']) ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap split">
        <div class="split__side">
            <?= $this->rule($this->t('Sources'), $this->plural(count($watch['source'] ?? []), 'source')) ?>
        </div>
        <div class="split__main">
            <ul class="rows" data-reveal>
                <?php foreach ($watch['source'] ?? [] as $source): ?>
                    <li class="row">
                        <a class="row__link" href="<?= $this->e((string) $source->url) ?>" target="_blank" rel="noopener">
                            <span class="row__name"><?= $this->e($source->title) ?></span>
                            <span class="row__desc"><?= $this->e($source->body) ?></span>
                            <span class="row__meta"><?= $this->e($source->meta) ?> ↗</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

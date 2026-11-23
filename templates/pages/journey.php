<?php
/**
 * @var App\View\View $this
 * @var list<App\Domain\Milestone> $milestones
 */
use App\Domain\Milestone;

$cap = $this->t('Systèmes, réseaux & bases de données');

$reached = array_values(array_filter($milestones, static fn (Milestone $m): bool => $m->state !== 'next'));
$planned = array_values(array_filter($milestones, static fn (Milestone $m): bool => $m->state === 'next'));

// Le profil et les cartes suivent l'ordre chronologique.
$chrono = $milestones;
usort($chrono, static fn (Milestone $a, Milestone $b): int => $a->at <=> $b->at);
?>
<section class="page-head page-head--journey">
    <div class="wrap journey-head">
        <div class="journey-head__title">
            <p class="crumb" data-reveal><a href="<?= $this->url('home') ?>">~</a> / <?= $this->t('parcours') ?></p>
            <h1 class="h1" data-reveal><?= $this->t('Parcours') ?><span class="title-semi" aria-hidden="true">;</span></h1>
        </div>

        <dl class="resume" data-reveal>
            <?php foreach ($reached as $m): ?>
                <div class="resume__row<?= $m->state === 'current' ? ' is-current' : '' ?>">
                    <dt><?= $this->e($m->title) ?></dt>
                    <span class="resume__dots" aria-hidden="true"></span>
                    <dd><?= $this->e($m->period) ?></dd>
                </div>
            <?php endforeach; ?>
            <?php if ($planned !== []): ?>
                <div class="resume__row is-next">
                    <dt><?= $this->t('En projet') ?></dt>
                    <span class="resume__dots" aria-hidden="true"></span>
                    <dd><?= $this->e(implode(' · ', array_map(static fn (Milestone $m): string => $m->title, $planned))) ?></dd>
                </div>
            <?php endif; ?>
        </dl>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap">
        <?= $this->rule($this->t("Le profil de l'étape")) ?>

        <div class="profile-scroll" data-profile-scroll data-reveal>
            <?= $this->journeyProfile($milestones, $cap) ?>
        </div>

        <ol class="legs" data-legs>
            <?php foreach ($chrono as $i => $m): ?>
                <li class="leg is-<?= $this->e($m->state) ?>" data-step="<?= $i ?>" data-reveal>
                    <p class="leg__top">
                        <span class="leg__num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="leg__state"><?= $this->t($m->stateLabel()) ?></span>
                    </p>
                    <p class="leg__period"><?= $this->e($m->period) ?> · <?= $this->e($m->kind) ?></p>
                    <h2 class="leg__title"><?= $this->e($m->title) ?></h2>
                    <?= $this->text($m->body) ?>
                    <?php if ($m->tags !== []): ?>
                        <ul class="chips"><?php foreach ($m->tags as $tag): ?><li><?= $this->e($tag) ?></li><?php endforeach; ?></ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <a class="card-link" href="<?= $this->url('journey-e5') ?>" data-reveal>
            <span class="card-link__kicker"><?= $this->t('BTS SIO · épreuve E5') ?></span>
            <span class="card-link__title"><?= $this->t('Support de présentation') ?> <span aria-hidden="true">→</span></span>
            <span class="card-link__text"><?= $this->t("Les diapositives présentées à l'épreuve, consultables en plein écran.") ?></span>
        </a>
    </div>
</section>

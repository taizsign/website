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

    </div>

</section>

<section class="section section--tight">
    <div class="wrap">
        <?= $this->rule($this->t("Le profil de l'étape")) ?>

        <div class="profile-scroll" data-profile-scroll data-reveal>
            <?= $this->journeyProfile($milestones, $cap) ?>
        </div>

        <?php // le carnet de route : la date à gauche, un repère sur le fil, puis l'étape ; l'état se lit au repère ?>
        <ol class="legs" data-legs>
            <?php foreach ($chrono as $i => $m): ?>
                <li class="leg is-<?= $this->e($m->state) ?>" data-step="<?= $i ?>" data-reveal>
                    <p class="leg__when">
                        <span class="leg__period"><?= $this->e($m->period) ?></span>
                        <span class="leg__kind"><?= $this->e($m->kind) ?></span>
                        <?php if ($m->state === 'current'): ?><span class="leg__state"><?= $this->t($m->stateLabel()) ?></span><?php endif; ?>
                    </p>
                    <span class="leg__mark" aria-hidden="true"></span>
                    <div class="leg__body">
                        <h2 class="leg__title"><?= $this->e($m->title) ?></h2>
                        <?= $this->text($m->body) ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <a class="card-link" href="<?= $this->url('journey-e5') ?>" data-reveal>
            <span class="card-link__kicker"><?= $this->t('BTS SIO, épreuve E5') ?></span>
            <span class="card-link__title"><?= $this->t('Support de présentation') ?> <span aria-hidden="true">→</span></span>
            <span class="card-link__text"><?= $this->t("Les diapositives présentées à l'épreuve, consultables en plein écran.") ?></span>
        </a>
    </div>
</section>

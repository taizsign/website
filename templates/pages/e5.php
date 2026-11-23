<?php
/**
 * @var App\View\View $this
 * @var list<string> $slides
 */
?>
<?= $this->partial('partials/gate', ['key' => 'e5', 'text' => $this->t("Ce support a été présenté aux épreuves du BTS SIO de 2026 et n'a pas été mis à jour depuis.")]) ?>
<section class="page-head">
    <div class="wrap">
        <p class="crumb" data-reveal><a href="<?= $this->url('home') ?>">~</a> / <a href="<?= $this->url('journey') ?>"><?= $this->t('parcours') ?></a> / e5</p>
        <h1 class="h1" data-reveal><?= $this->t('Épreuve E5') ?></h1>
        <p class="lead lead--wide" data-reveal><?= $this->t("Le support présenté à l'épreuve. Flèches du clavier pour naviguer, <kbd>F</kbd> pour le plein écran.") ?></p>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap">
        <?php if ($slides === []): ?>
            <p class="lead"><?= $this->t('Les diapositives arrivent bientôt.') ?></p>
        <?php else: ?>
            <div class="deck" data-deck>
                <div class="deck__stage" data-deck-stage>
                    <?php foreach ($slides as $i => $slide): ?>
                        <img class="deck__slide<?= $i === 0 ? ' is-active' : '' ?>" src="<?= $this->asset($slide) ?>" alt="<?= $this->t('Diapositive {n}', ['n' => $i + 1]) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>" data-slide>
                    <?php endforeach; ?>
                </div>
                <div class="deck__controls">
                    <button class="deck__btn" type="button" data-deck-prev aria-label="<?= $this->t('Diapositive précédente') ?>">←</button>
                    <span class="deck__count"><span data-deck-current>1</span> / <?= count($slides) ?></span>
                    <button class="deck__btn" type="button" data-deck-next aria-label="<?= $this->t('Diapositive suivante') ?>">→</button>
                    <button class="deck__btn deck__btn--wide" type="button" data-deck-full><?= $this->t('Plein écran') ?></button>
                </div>
                <ol class="deck__thumbs">
                    <?php foreach ($slides as $i => $slide): ?>
                        <li><button type="button" class="<?= $i === 0 ? 'is-active' : '' ?>" data-deck-go="<?= $i ?>" aria-label="<?= $this->t('Aller à la diapositive {n}', ['n' => $i + 1]) ?>"><img src="<?= $this->asset($slide) ?>" alt="" loading="lazy"></button></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>
    </div>
</section>

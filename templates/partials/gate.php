<?php
/**
 * Avertissement à confirmer avant d'accéder à une page dont le contenu est daté.
 * Sans JavaScript, la fenêtre ne s'affiche pas et la page reste lisible.
 *
 * @var App\View\View $this
 * @var string $key   identifiant mémorisé pour la visite (une confirmation par page)
 * @var string $text  phrase d'avertissement
 */
?>
<div class="gate" data-gate="<?= $this->e($key) ?>" role="dialog" aria-modal="true" aria-labelledby="gate-title-<?= $this->e($key) ?>" hidden>
    <div class="gate__box">
        <p class="gate__kicker"><?= $this->t('Contenu daté') ?></p>
        <h2 class="gate__title" id="gate-title-<?= $this->e($key) ?>"><?= $this->t('Cette page date des <em>épreuves 2026</em>.') ?></h2>
        <p class="gate__text"><?= $this->e($text) ?></p>
        <div class="gate__actions">
            <button class="btn" type="button" data-gate-ok><?= $this->t('Accéder au contenu') ?></button>
            <a class="btn btn--ghost" href="<?= $this->url('home') ?>"><?= $this->t("Retour à l'accueil") ?></a>
        </div>
    </div>
</div>

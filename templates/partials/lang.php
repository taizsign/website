<?php
/**
 * Choix de la langue : deux drapeaux à côté du logo. La langue affichée est en couleur
 * et soulignée ; l'autre, en gris, est un lien vers la même page dans l'autre langue.
 * Le clic mémorise le choix (cookie posé par lang.js) : l'adresse racine en tient compte.
 *
 * @var App\View\View $this
 */
?>
<nav class="lang" aria-label="<?= $this->t('Langue du site') ?>" data-lang>
    <?php $first = true; foreach ($this->alternates() as $code => $alt): ?>
        <?php if (!$first): ?><span class="lang__sep" aria-hidden="true">/</span><?php endif; $first = false; ?>
        <?php if ($alt['current']): ?>
            <span class="lang__flag is-current" aria-current="true" title="<?= $this->e($alt['name']) ?>">
                <img src="<?= $this->asset('img/flags/' . $code . '.svg') ?>" alt="<?= $this->e($alt['name']) ?>" width="21" height="14">
            </span>
        <?php else: ?>
            <a class="lang__flag" href="<?= $this->e($alt['url']) ?>" hreflang="<?= $this->e($code) ?>" lang="<?= $this->e($code) ?>" title="<?= $this->e($alt['switch']) ?>" data-lang-to="<?= $this->e($code) ?>">
                <img src="<?= $this->asset('img/flags/' . $code . '.svg') ?>" alt="<?= $this->e($alt['switch']) ?>" width="21" height="14">
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>

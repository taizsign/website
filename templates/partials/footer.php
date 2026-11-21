<?php
/**
 * @var App\View\View $this
 * @var array<string, mixed> $site
 */
?>
<footer class="foot" id="contact">
    <div class="wrap">
        <?= $this->rule($this->t('Contact'), null, true, false) ?>

        <h2 class="foot__title"><?= $this->t('Un projet, une question,<br>un serveur qui ne répond plus&nbsp;?') ?></h2>

        <div class="foot__mail">
            <a class="foot__address" href="mailto:<?= $this->e($site['email']) ?>"><?= $this->e($site['email']) ?></a>
            <button class="copy" type="button" data-copy="<?= $this->e($site['email']) ?>" data-copied="<?= $this->t('Adresse copiée ✓') ?>">
                <span data-copy-label><?= $this->t("Copier l'adresse") ?></span>
            </button>
        </div>

        <div class="foot__grid">
            <div>
                <p class="foot__key"><?= $this->t('Ailleurs') ?></p>
                <ul class="foot__links">
                    <li><a class="foot__link" href="<?= $this->e($site['github']) ?>" rel="noopener" target="_blank">GitHub · <?= $this->e($site['github_user']) ?> ↗</a></li>
                    <li><a class="foot__link" href="<?= $this->e($site['x']['url']) ?>" rel="noopener" target="_blank">X · @<?= $this->e($site['x']['handle']) ?> ↗</a></li>
                    <li>
                        <button class="foot__link foot__link--copy" type="button" data-copy="<?= $this->e($site['discord']) ?>" data-copied="<?= $this->t('Pseudo copié ✓') ?>" title="<?= $this->t('Copier le pseudo Discord') ?>">
                            <span data-copy-label>Discord · @<?= $this->e($site['discord']) ?></span>
                        </button>
                    </li>
                </ul>
            </div>
            <div>
                <p class="foot__key"><?= $this->t('Basé à') ?></p>
                <p><?= $this->e($site['location']) ?></p>
            </div>
        </div>

        <div class="foot__bottom">
            <span>© <?= date('Y') ?> <?= $this->e($site['name']) ?></span>
        </div>
    </div>
</footer>

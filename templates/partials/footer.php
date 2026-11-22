<?php
/**
 * @var App\View\View $this
 * @var array<string, mixed> $site
 */
$pages = [
    ['projects', $this->t('Projets')],
    ['skills', $this->t('Compétences')],
    ['journey', $this->t('Parcours')],
    ['watch', $this->t('Veille')],
    ['lab', $this->t('Labo')],
];
$now = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
?>
<footer class="foot" id="contact">
    <div class="wrap">
        <?= $this->rule($this->t('Contact'), null, true, false) ?>

        <div class="foot__main">
            <div class="foot__pitch">
                <h2 class="foot__title"><?= $this->t("Au plaisir d'<em>échanger</em>.") ?></h2>
                <div class="foot__mail">
                    <a class="foot__address" href="mailto:<?= $this->e($site['email']) ?>"><?= $this->e($site['email']) ?></a>
                    <button class="copy copy--icon" type="button" data-copy="<?= $this->e($site['email']) ?>" aria-label="<?= $this->t("Copier l'adresse") ?>" title="<?= $this->t("Copier l'adresse") ?>">
                        <svg class="copy__clip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8.5" y="8.5" width="12" height="12" rx="2.5"/><path d="M15.5 8.5V6a2.5 2.5 0 0 0-2.5-2.5H6A2.5 2.5 0 0 0 3.5 6v7A2.5 2.5 0 0 0 6 15.5h2.5"/></svg>
                    </button>
                </div>
            </div>

            <div class="foot__side">
                <p class="foot__note">
                    <?= $this->t('Mon code est sur GitHub, sous le nom') ?>
                    <a class="foot__handle" href="<?= $this->e($site['github']) ?>" rel="noopener" target="_blank"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg><?= $this->e($site['github_user']) ?></a>.
                    <?= $this->t('On peut aussi me joindre sur Discord,') ?>
                    <button class="foot__handle" type="button" data-copy="<?= $this->e($site['discord']) ?>" data-copied="<?= $this->t('Pseudo copié ✓') ?>" title="<?= $this->t('Copier le pseudo Discord') ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.211.375-.445.865-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128c.126-.094.252-.192.372-.291a.074.074 0 0 1 .078-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.009c.12.099.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg><span data-copy-label>@<?= $this->e($site['discord']) ?></span></button>.
                </p>
                <p class="foot__time">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9.2"/><path d="M12 7v5l3.2 2"/></svg>
                    <span class="sr-only"><?= $this->t('Heure locale') ?></span>
                    <span><?= $this->e($site['location']) ?></span>
                    <span class="foot__now"><time data-clock><?= $now->format('H:i') ?></time></span>
                </p>
            </div>
        </div>

        <!-- un lapin saute le long de la ligne du bas, pour le plaisir -->
        <div class="meadow" aria-hidden="true">
            <div class="bunny">
                <span class="bunny__bubble">:D</span>
                <svg class="bunny__body" viewBox="0 0 44 32">
                    <ellipse cx="27.5" cy="5.5" rx="2.1" ry="6" transform="rotate(-18 27.5 5.5)"/>
                    <ellipse cx="32" cy="5.5" rx="2.1" ry="6" transform="rotate(8 32 5.5)"/>
                    <circle cx="5.5" cy="18.5" r="3.4"/>
                    <ellipse cx="17" cy="20.5" rx="11.5" ry="8.2"/>
                    <circle cx="30" cy="15" r="6.4"/>
                    <ellipse cx="11" cy="28.2" rx="5.6" ry="2.2"/>
                    <ellipse cx="25" cy="28.4" rx="2.8" ry="1.8"/>
                    <circle class="bunny__eye" cx="32.4" cy="13.8" r="1.1"/>
                </svg>
            </div>
        </div>

        <div class="foot__bottom">
            <span>© <?= date('Y') ?> <?= $this->e($site['name']) ?></span>
            <nav class="foot__nav" aria-label="<?= $this->t('Plan du site') ?>">
                <?php foreach ($pages as [$route, $label]): ?>
                    <a href="<?= $this->url($route) ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </nav>
            <span class="foot__end">
                <a href="<?= $this->e(rtrim((string) $site['github'], '/')) ?>/website" rel="noopener" target="_blank"><?= $this->t('Code source') ?> ↗</a>
                <a href="#top"><?= $this->t('Haut de page') ?> ↑</a>
            </span>
        </div>
    </div>
</footer>

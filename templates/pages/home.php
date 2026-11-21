<?php
/**
 * @var App\View\View $this
 * @var array<string, mixed> $site
 * @var list<App\Domain\Project> $projects
 * @var array<string, int> $figures
 * @var list<array<string, mixed>> $goals
 * @var ?App\Domain\Milestone $next
 */

// Chaque lettre du nom dans son propre span, pour l'apparition en cascade.
$letters = function (string $word, int $offset = 0): string {
    $html = '';
    foreach (mb_str_split($word) as $i => $char) {
        $html .= sprintf('<span class="ch" style="--i: %d">%s</span>', $offset + $i, $this->e($char));
    }

    return $html;
};
?>
<section class="hero" data-hero>
    <canvas class="hero__canvas" data-hero-canvas aria-hidden="true"></canvas>
    <svg class="hero__spark" aria-hidden="true"><g data-spark opacity="0"><path d="M0,-11 C1.2,-2 2,-1.2 11,0 C2,1.2 1.2,2 0,11 C-1.2,2 -2,1.2 -11,0 C-2,-1.2 -1.2,-2 0,-11 Z"/></g></svg>

    <div class="hero__meta">
        <ul class="hero__facts">
            <li>
                <!-- toque de diplômé -->
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 9.5 12 4.5l10 5-10 5z"/><path d="M6.5 11.8v4.1c0 1.5 2.5 2.8 5.5 2.8s5.5-1.3 5.5-2.8v-4.1M22 9.5v5"/></svg>
                <?= $this->t('Étudiant BTS SIO SLAM &amp; SISR') ?>
            </li>
            <li>
                <!-- la citadelle de Besançon -->
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 21h20M3.5 21V8h1.5v1.5h1.5V8H8v13M16 21V8h1.5v1.5H19V8h1.5v13M8 12.5h8M8 12.5V11h1.6v1.5M11.2 12.5V11h1.6v1.5M14.4 12.5V11H16M10.5 21v-3a1.5 1.5 0 0 1 3 0v3M18.25 8V3.5l2.5 1.1-2.5 1.1"/></svg>
                <?= $this->e($site['location']) ?>
            </li>
        </ul>
        <p class="hero__status">
            <span class="hero__heading">
                <?= $this->e($this->t($site['heading'])) ?>
                <svg class="hero__underline" viewBox="0 0 300 10" preserveAspectRatio="none" aria-hidden="true"><path d="M2 6.5C60 3.5 110 8 170 5.2S270 3.8 298 6"/></svg>
            </span>
        </p>
    </div>

    <div class="hero__bottom">
        <h1 class="hero__title" aria-label="Raphaël Jacquis">
            <span aria-hidden="true"><?= $letters('Raphaë') ?><span class="ch perch perch--tall" style="--i: 6">l<?= $this->partial('partials/butterfly') ?></span></span>
            <span aria-hidden="true"><?= $letters('Jacquis', 7) ?><span class="hero__semi">;</span></span>
        </h1>
        <div class="hero__intro">
            <p><?= $this->t("Étudiant en BTS SIO à Besançon. J'ai appris à construire des applications, et je m'oriente maintenant vers ce qui les fait tourner, les serveurs, le réseau et les bases de données.") ?></p>
            <div class="actions">
                <a class="btn" href="<?= $this->url('projects') ?>"><?= $this->t('Voir mes projets') ?>
                    <span class="arrow" aria-hidden="true"><?= $this->partial('partials/arrow') ?><?= $this->partial('partials/arrow') ?></span>
                </a>
                <a class="btn btn--ghost" href="<?= $this->e($site['github']) ?>" target="_blank" rel="noopener">GitHub</a>
            </div>
        </div>
    </div>

    <a class="hero__scroll" href="#intro" aria-label="<?= $this->t('Descendre') ?>"><span></span></a>
</section>

<!-- (01) En deux mots -->
<section class="section" id="intro">
    <div class="wrap">
        <?= $this->rule($this->t('En deux mots')) ?>

        <p class="statement" data-reveal>
            <?= $this->t("Je viens du <em>développement</em>. Je vais vers l'<em>infrastructure</em>. Entre les deux, une conviction&nbsp;: on écrit de meilleures applications quand on sait comment vivent le serveur, le réseau et la base qui les portent.") ?>
        </p>

        <div class="trio">
            <article class="trio__item" data-reveal>
                <p class="trio__key"><?= $this->t("Aujourd'hui") ?></p>
                <p><?= $this->t('BTS SIO, SLAM &amp; SISR. Deux applications livrées de bout en bout&nbsp;: base MySQL, API, client, et mise en production sur mon VPS.') ?></p>
            </article>
            <article class="trio__item" data-reveal>
                <p class="trio__key"><?= $this->t('Le cap') ?></p>
                <p><?= $this->t("Administration systèmes, réseaux et bases de données. Linux, Windows Server, Cisco, PostgreSQL&nbsp;: la liste est longue, je la prends dans l'ordre.") ?></p>
            </article>
            <article class="trio__item" data-reveal>
                <p class="trio__key"><?= $this->t('À côté') ?></p>
                <p><?= $this->t('Un homelab en construction (Proxmox, OPNsense) et un fer à souder, pour réparer et améliorer mon matériel moi-même.') ?></p>
            </article>
        </div>

        <dl class="figures" data-reveal>
            <div class="figure">
                <dt><span data-count="<?= $figures['acquired'] ?>"><?= $figures['acquired'] ?></span></dt>
                <dd><?= $this->t('compétences acquises') ?><code><?= $this->t("WHERE statut = 'acquis'") ?></code></dd>
            </div>
            <div class="figure">
                <dt><span data-count="<?= $figures['learning'] ?>"><?= $figures['learning'] ?></span></dt>
                <dd><?= $this->t("en cours d'apprentissage") ?><code><?= $this->t("WHERE statut = 'en cours'") ?></code></dd>
            </div>
            <div class="figure">
                <dt><span data-count="<?= $figures['goals'] ?>"><?= $figures['goals'] ?></span></dt>
                <dd><?= $this->t('objectifs pour la suite') ?><code><?= $this->t("WHERE statut = 'objectif'") ?></code></dd>
            </div>
            <div class="figure">
                <dt><span data-count="<?= $figures['technologies'] ?>"><?= $figures['technologies'] ?></span></dt>
                <dd><?= $this->t('technologies dans mes projets') ?><code>SELECT COUNT(*) FROM technology</code></dd>
            </div>
        </dl>
        <p class="figures__note" data-reveal><?= $this->t('Ces chiffres sortent de la base SQLite qui fait tourner ce site.') ?> <a class="link" href="<?= $this->url('skills') ?>"><?= $this->t('Interroger mes compétences →') ?></a></p>
    </div>
</section>

<!-- (02) Projets -->
<section class="section section--projects">
    <div class="wrap">
        <div class="section__head">
            <?= $this->rule($this->t('Projets'), $this->plural(count($projects), 'dossier')) ?>
            <h2 class="h2" data-reveal><?= $this->t('Du schéma de base<br>à la mise en production.') ?></h2>
        </div>

        <?= $this->partial('partials/project-list', ['projects' => $projects]) ?>
    </div>
</section>

<!-- (03) Le cap -->
<section class="section section--cap">
    <div class="wrap cap">
        <div class="cap__text">
            <?= $this->rule($this->t('En ce moment'), $this->plural(count($goals), 'objectif')) ?>
            <h2 class="h2" data-reveal><?= $this->t('Ce que je ne sais pas<br>encore faire.') ?></h2>
            <p class="lead" data-reveal><?= $this->t("Une liste honnête vaut mieux qu'une barre de progression gonflée. Voici les compétences recherchées en administration systèmes, réseaux et bases de données que je n'ai pas encore, et que je travaille.") ?></p>
            <?php if ($next !== null): ?>
                <a class="link" href="<?= $this->url('journey') ?>" data-reveal><?= $this->t('Voir le parcours et la suite →') ?></a>
            <?php endif; ?>
        </div>

        <ol class="goals" data-reveal>
            <?php foreach ($goals as $i => $goal): ?>
                <li class="goal">
                    <span class="goal__num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <span class="goal__name"><?= $this->e($goal['name']) ?></span>
                    <span class="goal__domain"><?= $this->e($goal['domain_label']) ?></span>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

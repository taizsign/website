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
                <?= $this->t('Étudiant BTS SIO SLAM') ?>
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
            <p><?= $this->t("J'ai appris à construire des applications et m'oriente maintenant vers ce qui les fait tourner. <em>Je recherche une alternance</em> pour continuer dans cette voie.") ?></p>
            <div class="actions">
                <a class="btn" href="<?= $this->url('projects') ?>"><?= $this->t('Voir mes projets') ?>
                    <span class="arrow" aria-hidden="true"><?= $this->partial('partials/arrow') ?><?= $this->partial('partials/arrow') ?></span>
                </a>
                <a class="btn btn--ghost" href="<?= $this->e($site['github']) ?>" target="_blank" rel="noopener">GitHub</a>
            </div>
        </div>
    </div>

    <a class="hero__scroll" href="#intro" aria-label="<?= $this->t('Descendre') ?>"><span></span></a>
    <!-- une flèche dessinée à la main, du J de Jacquis vers le bas, si l'on reste en haut -->
    <svg class="nudge" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" data-nudge>
        <path class="nudge__shaft" pathLength="1"/>
        <path class="nudge__head" pathLength="1"/>
    </svg>
</section>

<!-- (01) Qui je suis -->
<section class="section" id="intro">
    <div class="wrap">
        <?= $this->rule($this->t('Qui je suis ?')) ?>

        <p class="statement" data-reveal>
            <?= $this->t("En 2019 j'ai monté mon premier PC. Depuis, l'informatique est devenue une passion. Elle m'a amené au <em>développement</em> et me pousse aujourd'hui vers l'<em>administration systèmes et réseaux</em>. Chez moi tourne un homelab avec NAS, OPNsense, Proxmox. À côté, un PC pensé pour l'<em>OPSEC</em>, dont j'ai retiré les puces à risque. Il démarre sous Kicksecure, chiffre ses données avec VeraCrypt, sort sur Internet par Mullvad.") ?>
        </p>

        <div class="trio">
            <article class="trio__item" data-reveal>
                <p class="trio__key"><?= $this->t("Aujourd'hui") ?></p>
                <p><?= $this->t("<em>Deux applications livrées</em> de bout en bout, de la base MySQL à la mise en production sur mon VPS. Prochaine étape, <em>une alternance</em> pour passer de l'autre côté du serveur.") ?></p>
            </article>
            <article class="trio__item" data-reveal>
                <p class="trio__key"><?= $this->t('Le cap') ?></p>
                <p><?= $this->t("Valider l'<em>AZ-900</em>, automatiser mon homelab avec <em>Ansible</em> et le superviser avec <em>Grafana</em>, pour qu'il tourne comme un vrai parc.") ?></p>
            </article>
            <article class="trio__item" data-reveal>
                <p class="trio__key"><?= $this->t('À côté') ?></p>
                <p><?= $this->t('Je répare mon matériel moi-même, <em>soudure</em> comprise. En parallèle, je prépare le <em>TOEIC</em> ainsi que la certification <em>Cisco CCNA</em>.') ?></p>
            </article>
        </div>

        <?php
        $rows = [
            ['acquired', $this->t('compétences acquises'), $this->t("WHERE statut = 'acquis'")],
            ['learning', $this->t("en cours d'apprentissage"), $this->t("WHERE statut = 'en cours'")],
            ['goals', $this->t('objectifs pour la suite'), $this->t("WHERE statut = 'objectif'")],
            ['technologies', $this->t('outils et technologies dans mes 2 projets'), 'SELECT COUNT(*) FROM technology'],
        ];
        ?>
        <dl class="figures" data-reveal>
            <?php foreach ($rows as [$key, $label, $query]): ?>
                <div class="figure">
                    <dt>
                        <span data-count="<?= $figures[$key] ?>"><?= $figures[$key] ?></span>
                        <button class="figure__help" type="button" aria-label="<?= $this->t('Voir la liste') ?>" aria-expanded="false" aria-controls="figure-<?= $key ?>">?</button>
                    </dt>
                    <dd><?= $label ?><code><?= $query ?></code></dd>
                    <div class="figure__pop" id="figure-<?= $key ?>">
                        <input class="figure__search" type="search" placeholder="<?= $this->t('Rechercher') ?>" aria-label="<?= $this->t('Rechercher dans la liste') ?>" autocomplete="off" spellcheck="false">
                        <ul class="figure__list">
                            <?php foreach ($details[$key] as $name): ?>
                                <li><?= $this->e($name) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </dl>
        <p class="figures__note" data-reveal><?= $this->t('Ces chiffres sortent de la base SQLite qui fait tourner ce site.') ?> <a class="link" href="<?= $this->url('skills') ?>"><?= $this->t('Interroger mes compétences →') ?></a></p>
    </div>
    <a class="hero__scroll" href="#projets" aria-label="<?= $this->t('Descendre') ?>"><span></span></a>
</section>

<!-- (02) Projets -->
<section class="section section--projects" id="projets">
    <div class="wrap">
        <div class="section__head projects-head">
            <?= $this->rule($this->t('Projets'), $this->plural(count($projects), 'dossier')) ?>
            <h2 class="h2" data-reveal><?= $this->t('Du <em>schéma de base</em><br>à la <em>mise en production</em>.') ?></h2>
        </div>

        <?= $this->partial('partials/project-list', ['projects' => $projects]) ?>
    </div>
    <a class="hero__scroll" href="#cap" aria-label="<?= $this->t('Descendre') ?>"><span></span></a>
</section>

<!-- (03) Le cap -->
<section class="section section--cap" id="cap">
    <div class="wrap">
        <?= $this->rule($this->t('En ce moment'), $this->plural(count($goals), 'objectif')) ?>
        <div class="cap">
            <div class="cap__text">
                <h2 class="h2 cap__title" data-reveal><?= $this->t('Ce que je ne sais <em>pas encore</em> faire.') ?></h2>
                <p class="lead" data-reveal><?= $this->t("Une liste honnête vaut mieux qu'une barre de progression gonflée. Voici les compétences recherchées en administration systèmes, réseaux et bases de données que je n'ai pas encore, et que je travaille.") ?></p>
                <?php if ($next !== null): ?>
                    <a class="link" href="<?= $this->url('journey') ?>" data-reveal><?= $this->t('Voir le parcours et la suite →') ?></a>
                <?php endif; ?>
            </div>

            <div class="term apt" data-reveal>
                <div class="term__bar"><span></span><span></span><span></span><em>bash · raphael@besancon</em></div>
                <div class="apt__body">
                    <p class="apt__line"><span class="apt__user">raphael@besancon</span>:<span class="apt__path">~</span>$ apt list --upgradable</p>
                    <ol class="apt__list">
                        <?php foreach ($goals as $goal): ?>
                            <?php $pkg = trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower((string) iconv('UTF-8', 'ASCII//TRANSLIT', $goal['name']))), '-'); ?>
                            <li class="apt__row">
                                <span class="apt__pkg"><?= $this->e($pkg) ?><span class="apt__suite">/<?= $this->e($goal['domain_label']) ?></span></span>
                                <span class="apt__state"><?= $this->t('[pas encore installé]') ?></span>
                                <span class="apt__name"><?= $this->e($goal['name']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                    <p class="apt__line apt__dim"><?= count($goals) ?> <?= $this->t('paquets à installer.') ?></p>
                    <p class="apt__line"><span class="apt__user">raphael@besancon</span>:<span class="apt__path">~</span>$ <span class="apt__caret" aria-hidden="true"></span></p>
                </div>
            </div>
        </div>
    </div>
    <a class="hero__scroll" href="#contact" aria-label="<?= $this->t('Descendre') ?>"><span></span></a>
</section>

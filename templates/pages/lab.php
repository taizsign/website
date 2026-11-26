<?php
/**
 * @var App\View\View $this
 * @var list<App\Domain\LabUnit> $units
 * @var list<array<string, mixed>> $services
 * @var App\Domain\Topology $infra
 * @var bool $showHomelab
 * @var bool $showProbes
 */
$totalU = array_sum(array_map(static fn ($u): int => $u->height, $units));  // 0 si le homelab est masqué
$rackU = max(12, $totalU + 2);
?>
<section class="page-head">
    <div class="wrap">
        <div class="lab-head">
            <div>
                <p class="crumb" data-reveal><a href="<?= $this->url('home') ?>">~</a> / <?= $this->t('labo') ?></p>
                <h1 class="h1" data-reveal><?= $this->t('Labo') ?><span class="title-semi" aria-hidden="true">;</span></h1>
            </div>

            <?php if (!$showHomelab || !$showProbes): ?>
                <aside class="works" data-reveal aria-label="<?= $this->t('Page en travaux') ?>">
                    <?= $this->partial('partials/barrier') ?>
                    <p class="works__text">
                        <strong><?= $this->t('En travaux.') ?></strong>
                        <?= $this->t('Prochainement ici&nbsp;:') ?>
                        <?php if (!$showHomelab): ?><span class="works__item"><?= $this->t('mon homelab') ?></span><?php endif; ?>
                        <?php if (!$showHomelab && !$showProbes): ?> <?= $this->t('et') ?> <?php endif; ?>
                        <?php if (!$showProbes): ?><span class="works__item"><?= $this->t('un ping en direct vers mes propres serveurs') ?></span><?php endif; ?>.
                    </p>
                </aside>
            <?php endif; ?>
        </div>
        <p class="lead lead--wide" data-reveal><?= $this->t('Le côté systèmes et réseau&nbsp;: ce qui tourne déjà en production sous mon nom de domaine, et les outils que je me fabrique.') ?></p>
    </div>
</section>

<!-- (01) Production -->
<section class="section section--tight">
    <div class="wrap">
        <div class="section__head">
            <?= $this->rule($this->t('En production'), 'raphael-jacquis.fr') ?>
            <h2 class="h2" data-reveal><?= $this->t('Un VPS,<br>un domaine, trois services.') ?></h2>
        </div>
        <figure class="arch" data-reveal>
            <?= $this->topology($infra, 'topo-infra') ?>
            <figcaption><?= $this->t("Un VPS OVH sous Ubuntu sert plusieurs sous-domaines de raphael-jacquis.fr grâce aux hôtes virtuels nginx. Le code arrive par GitHub&nbsp;: déploiement SSH pour le VPS, GitHub Pages pour le script d'installation.") ?></figcaption>
        </figure>

        <?php if ($showProbes): ?>
        <div class="probes" data-reveal>
            <div class="probes__head">
                <?= $this->rule($this->t('État des services'), $this->t('relevé toutes les 5 min'), false, false) ?>
                <p class="probes__note"><?= $this->t("Sonde HTTP lancée par ce serveur. Chaque barre de l'historique est un relevé.") ?></p>
            </div>
            <table class="probes__table">
                <thead>
                    <tr>
                        <th scope="col"><?= $this->t('service') ?></th>
                        <th scope="col"><?= $this->t('état') ?></th>
                        <th scope="col"><?= $this->t('réponse') ?></th>
                        <th scope="col"><?= $this->t('historique') ?></th>
                        <th scope="col"><?= $this->t('dispo.') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service):
                        $last = $service['last'];
                        $up = $last !== null && (bool) $last['ok'];
                    ?>
                        <tr>
                            <td><strong><?= $this->e($service['name']) ?></strong><span><?= $this->e($this->t($service['role'])) ?></span></td>
                            <td data-label="<?= $this->t('état') ?>">
                                <?php if ($last === null): ?>
                                    <span class="probe probe--unknown"><?= $this->t('pas encore relevé') ?></span>
                                <?php elseif ($up): ?>
                                    <span class="probe probe--up"><?= $this->t('en ligne') ?></span>
                                <?php else: ?>
                                    <span class="probe probe--down"><?= $this->t('injoignable') ?></span>
                                <?php endif; ?>
                            </td>
                            <td data-label="<?= $this->t('réponse') ?>" class="probes__mono">
                                <?php if ($last !== null): ?>
                                    <?= $last['http_code'] > 0 ? 'HTTP ' . (int) $last['http_code'] : $this->t('délai dépassé') ?>
                                    , <?= $this->number((float) $last['ms']) ?>&nbsp;ms
                                <?php else: ?><?= $this->t('pas de relevé') ?><?php endif; ?>
                            </td>
                            <td data-label="<?= $this->t('historique') ?>">
                                <span class="spark" aria-label="<?= $this->plural(count($service['history']), 'relevé') ?>">
                                    <?php foreach ($service['history'] as $p): ?>
                                        <i class="<?= $p['ok'] ? 'ok' : 'ko' ?>" title="<?= $this->e($p['checked_at']) ?>, <?= (int) $p['http_code'] ?>"></i>
                                    <?php endforeach; ?>
                                </span>
                            </td>
                            <td data-label="<?= $this->t('dispo.') ?>" class="probes__mono"><?= $service['uptime'] === null ? 'n/a' : $this->number($service['uptime'], 1) . '&nbsp;%' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if ($showHomelab): ?>
<!-- (02) Homelab -->
<section class="section section--dark">
    <div class="wrap lab">
        <div class="lab__text">
            <?= $this->rule($this->t('Homelab'), $this->plural(count($units), 'élément'), true) ?>
            <h2 class="h2" data-reveal><?= $this->t('La baie,<br>en construction.') ?></h2>
            <p class="lead" data-reveal><?= $this->t("Le homelab, c'est l'endroit où je peux tout casser. Chaque brique sert à pratiquer une compétence d'administration avant d'en avoir besoin pour de vrai. Survolez un élément.") ?></p>
            <ul class="lab__legend" data-reveal>
                <li><i class="led is-up"></i><?= $this->t('en place') ?></li>
                <li><i class="led is-building"></i><?= $this->t('en cours') ?></li>
                <li><i class="led is-planned"></i><?= $this->t('prévu') ?></li>
            </ul>
            <p class="lab__detail" aria-live="polite" data-rack-detail><?= $this->t('Survolez ou sélectionnez un élément de la baie.') ?></p>
        </div>

        <div class="rack" style="--u: <?= $rackU ?>" data-reveal data-rack>
            <div class="rack__rail" aria-hidden="true">
                <?php for ($i = $rackU; $i >= 1; $i--): ?><span><?= $i ?></span><?php endfor; ?>
            </div>
            <ul class="rack__units">
                <?php foreach ($units as $unit): ?>
                    <li class="unit <?= $unit->statusClass() ?>" style="--h: <?= $unit->height ?>">
                        <button type="button" class="unit__face" data-note="<?= $this->e($unit->role . ', ' . $unit->name . $this->t(' : ') . $unit->note) ?>">
                            <span class="unit__screws" aria-hidden="true"></span>
                            <span class="unit__role"><?= $this->e($unit->role) ?></span>
                            <span class="unit__name"><?= $this->e($unit->name) ?></span>
                            <span class="unit__status"><i class="led <?= $unit->statusClass() ?>"></i><?= $this->t($unit->status) ?></span>
                            <span class="unit__vents" aria-hidden="true"></span>
                        </button>
                    </li>
                <?php endforeach; ?>
                <?php for ($i = 0; $i < $rackU - $totalU; $i++): ?>
                    <li class="unit unit--blank" style="--h: 1" aria-hidden="true"><span class="unit__face"></span></li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- (03) Outil -->
<section class="section">
    <div class="wrap split">
        <div class="split__side">
            <?= $this->rule($this->t('Outil maison'), 'PowerShell') ?>
            <h2 class="h3" data-reveal>setup-computer</h2>
            <p class="split__summary" data-reveal><?= $this->t("Préparer un poste Windows neuf en une seule commande, au lieu d'une heure de clics.") ?></p>
            <a class="link" href="https://github.com/taizsign/setup-computer" target="_blank" rel="noopener" data-reveal><?= $this->t('Voir le script sur GitHub ↗') ?></a>
        </div>
        <div class="split__main">
            <div class="term" data-reveal>
                <div class="term__bar"><span></span><span></span><span></span><em><?= $this->t('PowerShell (administrateur)') ?></em></div>
                <pre class="term__body"><span class="term__prompt">PS&gt;</span> irm https://setup.raphael-jacquis.fr/setup.ps1 | iex</pre>
            </div>
            <ol class="steps" data-reveal>
                <li><span><?= $this->t("<strong>Se relance en administrateur</strong> si besoin, et vérifie qu'on est au moins sous Windows 10.") ?></span></li>
                <li><span><?= $this->t("<strong>Choisit son gestionnaire de paquets</strong>&nbsp;: winget s'il est présent, sinon installe Chocolatey.") ?></span></li>
                <li><span><?= $this->t('<strong>Installe les logiciels et les runtimes</strong>&nbsp;: navigateur, lecteur PDF, suite bureautique, VLC, Visual C++, .NET Desktop Runtime.') ?></span></li>
                <li><span><?= $this->t("<strong>Applique quelques réglages</strong>&nbsp;: plan d'alimentation haute performance, Game Bar désactivée, télémétrie réduite.") ?></span></li>
                <li><span><?= $this->t('<strong>Propose le pilote graphique</strong> NVIDIA ou AMD, au choix.') ?></span></li>
            </ol>
        </div>
    </div>
</section>

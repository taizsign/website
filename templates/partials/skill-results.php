<?php
/**
 * Résultats de la console sous forme de matrice de compétences :
 * une ligne par domaine, une colonne par niveau (de « à apprendre » à « expert »).
 * Le détail et les preuves de chaque compétence s'ouvrent au survol, au focus ou au toucher.
 *
 * Rendu côté serveur dans tous les cas : la console récupère ce même HTML en JSON
 * quand on modifie la requête, l'affichage est identique avec ou sans JavaScript.
 *
 * Chaque ligne a des clés fixes (name, domain, level, status, proof, detail, projects, logo) :
 * domain et status sont les valeurs stockées, domain_label et status_label celles de la requête affichée.
 *
 * @var App\View\View $this
 * @var list<array<string, mixed>> $rows
 * @var array<string, string> $projects  slug => titre
 */
use App\Service\SkillQuery;

$domains = [
    'systèmes' => $this->t('Systèmes'),
    'réseaux'  => $this->t('Réseaux'),
    'bdd'      => $this->t('Bases de données'),
    'dev'      => $this->t('Développement'),
    'outils'   => $this->t('Outils et cloud'),
];
$levels = [
    0 => $this->t('À apprendre'),
    1 => $this->t('Notions'),
    2 => $this->t('En pratique'),
    3 => $this->t('Autonome'),
    4 => $this->t('Confirmé'),
    5 => $this->t('Expert'),
];

// domaine => niveau => compétences (dans l'ordre renvoyé par la requête)
$matrix = [];
foreach (SkillQuery::DOMAINS as $domain) {
    $matrix[$domain] = array_fill_keys(array_keys($levels), []);
}
foreach ($rows as $row) {
    $matrix[$row['domain']][$row['level']][] = $row;
}
$matrix = array_filter($matrix, static fn (array $cells): bool => array_merge(...array_values($cells)) !== []);

$summary = function (array $cells): string {
    $count = array_count_values(array_column(array_merge(...array_values($cells)), 'status'));
    $parts = [];
    if (isset($count['acquis'])) {
        $parts[] = $this->plural($count['acquis'], 'acquise');
    }
    if (isset($count['en cours'])) {
        $parts[] = $this->t('{n}&nbsp;en cours', ['n' => $count['en cours']]);
    }
    if (isset($count['objectif'])) {
        $parts[] = $this->plural($count['objectif'], 'objectif');
    }

    return implode(', ', $parts);
};

$uid = 0;
?>
<?php if ($matrix === []): ?>
    <p class="skills-empty"><?= $this->t('Aucune ligne. La requête est valide, elle ne renvoie simplement rien&nbsp;: réactivez une valeur.') ?></p>
<?php else: ?>
    <div class="skills-bar">
        <p class="skills-guide"><span class="skills-guide__wide"><?= $this->t('Lecture de gauche à droite, du niveau le plus faible au plus élevé.') ?></span><span class="skills-guide__narrow"><?= $this->t('Classé du niveau le plus élevé au plus faible.') ?></span> <span class="skills-guide__hint"><?= $this->t('Survolez ou touchez une compétence pour en savoir plus.') ?></span></p>
    </div>

    <div class="matrix" role="table" aria-label="<?= $this->t('Matrice des compétences : domaines en lignes, niveaux en colonnes') ?>">
        <div class="matrix__row matrix__row--head" role="row" data-reveal>
            <div class="matrix__corner" role="columnheader"><span class="sr-only"><?= $this->t('Domaine') ?></span></div>
            <?php foreach ($levels as $level => $label): ?>
                <div class="matrix__level" role="columnheader" data-level="<?= $level ?>" style="--col: <?= $level ?>">
                    <?= $this->stars($level) ?>
                    <strong><?= $label ?></strong>
                </div>
            <?php endforeach; ?>
        </div>

        <?php foreach ($matrix as $domain => $cells): ?>
            <div class="matrix__row" role="row">
                <div class="matrix__domain" role="rowheader">
                    <strong><?= $domains[$domain] ?></strong>
                    <span><?= $summary($cells) ?></span>
                </div>

                <?php foreach ($cells as $level => $skills): ?>
                    <div class="matrix__cell" role="cell" data-level="<?= $level ?>"<?= $skills === [] ? ' data-empty' : '' ?>>
                        <?php if ($skills !== []): ?>
                            <div class="matrix__tag" aria-hidden="true"><?= $this->stars($level) ?><span><?= $levels[$level] ?></span></div>
                            <ul class="sk-list">
                                <?php foreach ($skills as $row):
                                    $id = 'sk-' . ++$uid;
                                    $status = str_replace(' ', '-', $row['status']);
                                    $proofs = $row['proof'] === null ? [] : explode(' | ', $row['proof']);
                                    $linked = array_intersect_key($projects, array_flip($row['projects']));
                                ?>
                                    <li class="sk sk--<?= $this->e($status) ?><?= $level >= 4 ? ' sk--end' : '' ?>" style="--i: <?= $uid ?>">
                                        <button class="sk__btn" type="button" aria-describedby="<?= $id ?>">
                                            <img class="sk__logo" src="<?= $this->asset('img/logos/' . $row['logo'] . '.svg') ?>" alt="" width="20" height="20" loading="lazy">
                                            <span class="sk__name"><?= $this->e($row['name']) ?></span>
                                        </button>
                                        <div class="sk__pop" id="<?= $id ?>">
                                            <div class="sk__pop-head">
                                                <img src="<?= $this->asset('img/logos/' . $row['logo'] . '.svg') ?>" alt="" width="32" height="32" loading="lazy">
                                                <div>
                                                    <strong><?= $this->e($row['name']) ?></strong>
                                                    <span class="sk__rating"><?= $this->stars($level) ?><?= $levels[$level] ?>, <?= $this->e($row['status_label']) ?></span>
                                                </div>
                                            </div>
                                            <p class="sk__detail"><?= $this->e($row['detail']) ?></p>
                                            <?php if ($proofs !== []): ?>
                                                <p class="sk__proof"><?php foreach ($proofs as $k => $proof): $slug = array_search($proof, $linked, true); ?><?= $k > 0 ? '<span class="sk__sep">,</span> ' : '' ?><?php if ($slug !== false): ?><a href="<?= $this->url('project', ['slug' => (string) $slug]) ?>"><?= $this->e($proof) ?></a><?php else: ?><span><?= $this->e($proof) ?></span><?php endif; ?><?php endforeach; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

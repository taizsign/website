<?php
/**
 * @var App\View\View $this
 * @var App\Service\SkillQuery $query
 * @var array{sql: string, rows: list<array<string, mixed>>, count: int, ms: float} $result
 * @var array<string, string> $projects
 * @var string $engine
 */
$domainParam = $this->e($query->param('domain'));
$statusParam = $this->e($query->param('status'));
?>
<section class="page-head">
    <div class="wrap">
        <p class="crumb" data-reveal><a href="<?= $this->url('home') ?>">~</a> / <?= $this->t('compétences') ?></p>
        <h1 class="h1" data-reveal><?= $this->t('Compétences') ?></h1>
        <p class="lead lead--wide" data-reveal><?= $this->t("Pas de jauges au hasard&nbsp;: mes compétences sont rangées dans une vraie base de données, chacune avec un niveau, un statut et une preuve. Cliquez sur les valeurs de la requête pour filtrer&nbsp;; c'est exactement cette requête que le serveur exécute.") ?></p>
    </div>
</section>

<section class="section section--tight">
    <div class="wrap">

        <form class="console" action="<?= $this->url('skills') ?>" method="get" data-console data-endpoint="<?= $this->url('skills-query') ?>">
            <div class="console__bar">
                <span class="console__file"><i></i><?= $this->e($query->view()) ?>.sql</span>
                <span class="console__engine"><?= $this->e($engine) ?></span>
                <button class="console__run" type="submit"><?= $this->t('Exécuter') ?> <kbd>▸</kbd></button>
            </div>

            <div class="console__query" role="group" aria-label="<?= $this->t('Requête SQL modifiable') ?>">
                <div class="ql"><span class="ln">1</span><span><span class="tok-kw">SELECT</span> <?= $this->e($query->selectList()) ?></span></div>
                <div class="ql"><span class="ln">2</span><span><span class="tok-kw">FROM</span> <?= $this->e($query->view()) ?></span></div>
                <div class="ql"><span class="ln">3</span><span>
                    <span class="tok-kw">WHERE</span> <?= $this->e($query->column('domain')) ?> <span class="tok-kw">IN</span> (<input type="hidden" name="<?= $domainParam ?>[]" value=""><?php
                    $i = 0; foreach ($query->domainValues() as $domain => $shown): ?><?= $i++ > 0 ? ', ' : '' ?><label class="tk"><input type="checkbox" name="<?= $domainParam ?>[]" value="<?= $this->e($shown) ?>"<?= in_array($domain, $query->domains, true) ? ' checked' : '' ?>><span class="tok-string">'<?= $this->e($shown) ?>'</span></label><?php
                    endforeach; ?>)
                </span></div>
                <div class="ql"><span class="ln">4</span><span>
                    &nbsp;&nbsp;<span class="tok-kw">AND</span> <?= $this->e($query->column('status')) ?> <span class="tok-kw">IN</span> (<input type="hidden" name="<?= $statusParam ?>[]" value=""><?php
                    $i = 0; foreach ($query->statusValues() as $status => $shown): ?><?= $i++ > 0 ? ', ' : '' ?><label class="tk"><input type="checkbox" name="<?= $statusParam ?>[]" value="<?= $this->e($shown) ?>"<?= in_array($status, $query->statuses, true) ? ' checked' : '' ?>><span class="tok-string">'<?= $this->e($shown) ?>'</span></label><?php
                    endforeach; ?>)
                </span></div>
                <div class="ql"><span class="ln">5</span><span>
                    <span class="tok-kw">ORDER BY</span>
                    <label class="tk-select">
                        <span class="sr-only"><?= $this->t('Ordre de tri') ?></span>
                        <select name="<?= $this->e($query->param('order')) ?>">
                            <?php foreach ($query->orderOptions() as $key => $text): ?>
                                <option value="<?= $this->e($key) ?>"<?= $query->selectedOrder() === $key ? ' selected' : '' ?>><?= $this->e($text) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>;
                </span></div>
                <div class="ql ql--comment"><span class="ln">6</span><span class="tok-comment"><?= $this->t("/* 'objectif' : compétences très demandées en systèmes, réseaux et BDD que je travaille en ce moment */") ?></span></div>
            </div>

            <div class="console__status" aria-live="polite" data-status>
                <span class="console__ok">▸</span>
                <strong data-count><?= $this->plural($result['count'], 'ligne') ?></strong>
                <span><?= $this->t('en') ?> <span data-ms><?= $this->number($result['ms'], 2) ?></span>&nbsp;ms</span>
                <details class="console__sql">
                    <summary><?= $this->t('requête exécutée') ?></summary>
                    <pre data-sql><?= $this->e($result['sql']) ?></pre>
                </details>
            </div>
        </form>

        <div class="result" data-result aria-live="polite">
            <?= $this->partial('partials/skill-results', ['rows' => $result['rows'], 'projects' => $projects]) ?>
        </div>
    </div>
</section>


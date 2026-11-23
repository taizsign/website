<?php
/**
 * @var App\View\View $this
 * @var list<App\Domain\Project> $projects
 */

?>
<ol class="projects">
    <?php foreach ($projects as $project): ?>
        <li class="project" data-reveal data-techs="<?= $this->e(mb_strtolower(implode('|', $this->techList($project)))) ?>">
            <a class="project__link" href="<?= $this->url('project', ['slug' => $project->slug]) ?>">
                <div class="project__text">
                    <p class="project__num"><?= $project->number() ?> <span>/ <?= $project->year ?> · <?= $this->e($project->context) ?></span></p>
                    <h3 class="project__title"><?= $this->breakable($project->title) ?></h3>
                    <p class="project__tagline"><?= $this->e($project->tagline) ?></p>
                    <ul class="chips" aria-label="<?= $this->t('Technologies principales') ?>">
                        <?php foreach ($project->headlineStack(4) as $tech): ?>
                            <li><img class="chips__logo" src="<?= $this->techLogo($tech) ?>" alt="" width="16" height="16" loading="lazy"><?= $this->e($tech) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    </span>
                </div>
                <figure class="project__figure">
                    <?= $this->topology($project->architecture, 'topo-' . $project->slug) ?>
                </figure>
                <span class="project__tag" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 7.5V18a1.5 1.5 0 0 0 1.5 1.5h15A1.5 1.5 0 0 0 21 18V9.5A1.5 1.5 0 0 0 19.5 8h-7.4l-2-2.5H4.5A1.5 1.5 0 0 0 3 7z"/><path d="M3 11h18"/></svg><?= $this->t('Lire le dossier') ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ol>

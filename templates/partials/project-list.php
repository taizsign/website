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
                    <span class="project__more"><?= $this->t('Lire le dossier') ?><span class="project__line" aria-hidden="true"></span><svg class="project__arrow" viewBox="0 0 12 12" aria-hidden="true"><path d="M1 6h9M6.5 2.5 10 6l-3.5 3.5"/></svg></span>
                </div>
                <figure class="project__figure">
                    <?= $this->topology($project->architecture, 'topo-' . $project->slug) ?>
                </figure>
            </a>
        </li>
    <?php endforeach; ?>
</ol>

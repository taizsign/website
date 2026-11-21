<?php
/**
 * @var App\View\View $this
 * @var list<App\Domain\Project> $projects
 */
?>
<ol class="projects">
    <?php foreach ($projects as $project): ?>
        <li class="project" data-reveal>
            <a class="project__link" href="<?= $this->url('project', ['slug' => $project->slug]) ?>">
                <div class="project__text">
                    <p class="project__num"><?= $project->number() ?> <span>/ <?= $project->year ?> · <?= $this->e($project->context) ?></span></p>
                    <h3 class="project__title"><?= $this->breakable($project->title) ?></h3>
                    <p class="project__tagline"><?= $this->e($project->tagline) ?></p>
                    <ul class="chips" aria-label="<?= $this->t('Technologies principales') ?>">
                        <?php foreach ($project->headlineStack(4) as $tech): ?>
                            <li><?= $this->e($tech) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <span class="project__more"><?= $this->t('Lire le dossier') ?> <span aria-hidden="true">→</span></span>
                </div>
                <figure class="project__figure">
                    <?= $this->topology($project->architecture, 'topo-' . $project->slug) ?>
                </figure>
            </a>
        </li>
    <?php endforeach; ?>
</ol>

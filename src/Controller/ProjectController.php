<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

final class ProjectController extends AbstractController
{
    public function index(Request $request): Response
    {
        return $this->render('pages/projects/index', [
            'title'       => $this->t('Projets'),
            'description' => $this->t('Mes projets de BTS SIO, une application C# avec son API REST et un site Symfony, présentés avec leur architecture et leur code source.'),
            'projects'    => $this->container->projects()->all(),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $repository = $this->container->projects();
        $project = $repository->findBySlug($slug);

        if ($project === null) {
            return $this->notFound();
        }

        return $this->render('pages/projects/show', [
            'title'       => $project->title,
            'description' => $project->tagline,
            'project'     => $project,
            'next'        => $repository->next($project),
        ]);
    }
}

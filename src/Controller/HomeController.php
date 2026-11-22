<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Milestone;
use App\Http\Request;
use App\Http\Response;
use App\Service\SkillQuery;

final class HomeController extends AbstractController
{
    public function index(Request $request): Response
    {
        $content = $this->container->content();
        $goals = (new SkillQuery(SkillQuery::DOMAINS, ['objectif'], 'domaine', $this->container->locale()))->run($this->container->db());
        $next = array_values(array_filter($content->milestones(), static fn (Milestone $m): bool => $m->state === 'next'));

        return $this->render('pages/home', [
            'title'    => null,
            'projects' => $this->container->projects()->all(),
            'figures'  => $content->figures(),
            'details'  => $content->figureNames(),
            'goals'    => $goals['rows'],
            'next'     => $next[0] ?? null,
        ]);
    }
}

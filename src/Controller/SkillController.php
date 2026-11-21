<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\SkillQuery;

final class SkillController extends AbstractController
{
    public function index(Request $request): Response
    {
        $query = $this->queryFrom($request);

        return $this->render('pages/skills', [
            'title'       => $this->t('Compétences'),
            'description' => $this->t('Mes compétences, stockées dans une base SQLite et interrogeables directement depuis la page.'),
            'query'       => $query,
            'result'      => $query->run($this->container->db()),
            'projects'    => $this->container->content()->projectNames(),
            'engine'      => $this->container->db()->engine(),
        ]);
    }

    /**
     * Même requête, réponse JSON : utilisée par la console sans recharger la page.
     * Le HTML des résultats est rendu ici par le même gabarit que la page complète.
     */
    public function query(Request $request): Response
    {
        $query = $this->queryFrom($request);
        $result = $query->run($this->container->db());

        $view = $this->container->view();
        $html = $view->partial('partials/skill-results', [
            'rows'     => $result['rows'],
            'projects' => $this->container->content()->projectNames(),
        ]);

        return Response::json([
            'sql'    => $result['sql'],
            'count'  => $result['count'],
            'label'  => $view->plural($result['count'], 'ligne'),  // « 11 lignes », « 11 rows »
            'ms'     => $result['ms'],
            'html'   => $html,
            'params' => $query->toQuery(),
        ])->withHeader('Cache-Control', 'no-store');
    }

    private function queryFrom(Request $request): SkillQuery
    {
        return SkillQuery::fromRequest($request, $this->container->locale());
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

final class WatchController extends AbstractController
{
    public function index(Request $request): Response
    {
        $feeds = [];
        foreach ($this->container->config['feeds'] as $feed) {
            $feeds[] = $feed + ['items' => $this->container->feeds()->latest($feed['url'], 4)];
        }

        return $this->render('pages/watch', [
            'title'       => $this->t('Veille'),
            'description' => $this->t('Veille technologique : l\'IA générative dans le développement logiciel, de l\'assistant à l\'agent autonome.'),
            'watch'       => $this->container->content()->watch(),
            'feeds'       => $feeds,
        ]);
    }
}

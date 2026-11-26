<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

final class JourneyController extends AbstractController
{
    public function index(Request $request): Response
    {
        return $this->render('pages/journey', [
            'title'       => $this->t('Parcours'),
            'description' => $this->t('Du bac STI2D au BTS SIO, et la suite : l\'administration systèmes, réseaux et bases de données.'),
            'milestones'  => $this->container->content()->milestones(),
        ]);
    }

    /**
     * Support de présentation de l'épreuve E5 (diapositives exportées en images).
     */
    public function e5(Request $request): Response
    {
        $dir = $this->container->root . '/public/assets/img/e5';
        $slides = glob($dir . '/*.webp') ?: [];
        natsort($slides);

        return $this->render('pages/e5', [
            'title'       => $this->t('Épreuve E5'),
            'description' => $this->t('Le support présenté à l\'épreuve E5 du BTS SIO en 2026, à feuilleter en plein écran.'),
            'slides'      => array_map(static fn (string $f): string => 'img/e5/' . basename($f), array_values($slides)),
        ]);
    }
}

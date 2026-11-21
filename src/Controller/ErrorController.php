<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

final class ErrorController extends AbstractController
{
    public function notFound(?Request $request = null): Response
    {
        return $this->render('pages/error', [
            'title' => $this->t('Page introuvable'),
            'code'  => 404,
            'path'  => $request?->path ?? $_SERVER['REQUEST_URI'] ?? '/',
        ], 404);
    }

    public function serverError(\Throwable $e): Response
    {
        $debug = (bool) $this->container->config['debug'];

        try {
            return $this->render('pages/error', [
                'title' => $this->t('Erreur'),
                'code'  => 500,
                'path'  => $_SERVER['REQUEST_URI'] ?? '/',
                'error' => $debug ? $e : null,
            ], 500);
        } catch (\Throwable) {
            // Si même la page d'erreur plante (base illisible…), réponse minimale.
            $detail = $debug ? '<pre>' . htmlspecialchars((string) $e) . '</pre>' : '';

            return Response::html('<!doctype html><meta charset="utf-8"><title>Erreur</title><h1>500</h1>' . $detail, 500);
        }
    }
}

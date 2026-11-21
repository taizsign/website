<?php

declare(strict_types=1);

namespace App;

use App\Controller\DocumentController;
use App\Controller\ErrorController;
use App\Controller\HomeController;
use App\Controller\JourneyController;
use App\Controller\LabController;
use App\Controller\ProjectController;
use App\Controller\SkillController;
use App\Controller\WatchController;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\I18n\Translator;

/**
 * Point d'entrée de l'application : déclare les routes, trouve le bon
 * contrôleur, et transforme toute erreur en page propre.
 */
final class Kernel
{
    private readonly float $startedAt;

    private function __construct(private readonly Container $container)
    {
        $this->startedAt = hrtime(true) / 1e6;
    }

    public static function boot(string $root): self
    {
        date_default_timezone_set('Europe/Paris');
        mb_internal_encoding('UTF-8');

        $config = require $root . '/config/app.php';

        error_reporting(E_ALL);
        ini_set('display_errors', $config['debug'] ? '1' : '0');

        return new self(new Container($root, $config, self::routes()));
    }

    /**
     * Les routes, en français, avec leur chemin anglais (servi sous /en).
     */
    public static function routes(): Router
    {
        return (new Router(Translator::prefixes()))
            ->get('/', [HomeController::class, 'index'], 'home')
            ->get('/projets', [ProjectController::class, 'index'], 'projects', ['en' => '/projects'])
            ->get('/projets/{slug}', [ProjectController::class, 'show'], 'project', ['en' => '/projects/{slug}'])
            ->get('/competences', [SkillController::class, 'index'], 'skills', ['en' => '/skills'])
            ->get('/competences/requete', [SkillController::class, 'query'], 'skills-query', ['en' => '/skills/query'])
            ->get('/parcours', [JourneyController::class, 'index'], 'journey', ['en' => '/journey'])
            ->get('/parcours/e5', [JourneyController::class, 'e5'], 'journey-e5', ['en' => '/journey/e5'])
            ->get('/veille', [WatchController::class, 'index'], 'watch', ['en' => '/tech-watch'])
            ->get('/labo', [LabController::class, 'index'], 'lab', ['en' => '/lab'])
            ->get('/documents/{slug}', [DocumentController::class, 'show'], 'document', localized: false);
    }

    public function handle(Request $request): Response
    {
        // Langue choisie avec les drapeaux (cookie posé par le navigateur) :
        // l'adresse racine mène directement à la version anglaise.
        if ($request->path === '/' && $request->cookie('lang') === 'en') {
            return Response::redirect($this->container->router->url('home', [], 'en'))
                ->withHeader('Vary', 'Cookie')
                ->withHeader('Cache-Control', 'no-store')
                ->withSecurityHeaders();
        }

        $match = $this->container->router->match($request->method, $request->path);
        $locale = $match['locale'] ?? Translator::fromPath($request->path);
        $this->container->useLocale($locale);

        try {
            $view = $this->container->view();
            $view->share('site', $this->container->config);
            $view->share('params', $match['params'] ?? []);

            if ($match === null) {
                $view->share('route', 'error');
                $response = (new ErrorController($this->container))->notFound($request);
            } else {
                $view->share('route', $match['name']);
                [$class, $method] = $match['action'];
                $response = (new $class($this->container))->$method($request, ...$match['params']);
            }
        } catch (\Throwable $e) {
            error_log((string) $e);
            $this->container->view()->share('route', 'error');
            $response = (new ErrorController($this->container))->serverError($e);
        }

        if ($request->path === '/') {
            $response = $response->withHeader('Vary', 'Cookie');
        }

        return $response
            ->withHeader('Content-Language', $locale)
            ->withHeader('Server-Timing', sprintf('app;dur=%.1f', $this->elapsed()))
            ->withSecurityHeaders();
    }

    private function elapsed(): float
    {
        return hrtime(true) / 1e6 - $this->startedAt;
    }
}

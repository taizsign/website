<?php

declare(strict_types=1);

namespace App\Controller;

use App\Container;
use App\Http\Response;

abstract class AbstractController
{
    public function __construct(protected readonly Container $container)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = [], int $status = 200): Response
    {
        return Response::html($this->container->view()->render($template, $data), $status);
    }

    protected function notFound(): Response
    {
        return (new ErrorController($this->container))->notFound();
    }

    /**
     * Texte dans la langue de la page (titres, descriptions).
     */
    protected function t(string $text): string
    {
        return $this->container->translator()->t($text);
    }
}

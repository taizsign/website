<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\I18n\Translator;

/**
 * Plan du site pour les moteurs de recherche : chaque page dans les deux langues,
 * avec ses équivalents (hreflang). Les documents PDF n'y sont pas, robots.txt les écarte.
 */
final class SitemapController extends AbstractController
{
    private const PAGES = ['home', 'projects', 'skills', 'journey', 'journey-e5', 'watch', 'lab'];

    public function index(Request $request): Response
    {
        $router = $this->container->router;
        $base = rtrim($this->container->config['url'], '/');

        $pages = array_map(static fn (string $name): array => [$name, []], self::PAGES);
        foreach ($this->container->projects()->all() as $project) {
            $pages[] = ['project', ['slug' => $project->slug]];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        foreach ($pages as [$name, $params]) {
            $urls = [];
            foreach (array_keys(Translator::LOCALES) as $locale) {
                $urls[$locale] = $base . $router->url($name, $params, $locale);
            }
            foreach ($urls as $url) {
                $xml .= "  <url>\n    <loc>" . htmlspecialchars($url, ENT_XML1) . "</loc>\n";
                foreach ($urls as $locale => $alternate) {
                    $xml .= '    <xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . htmlspecialchars($alternate, ENT_XML1) . '"/>' . "\n";
                }
                $xml .= "  </url>\n";
            }
        }
        $xml .= "</urlset>\n";

        return Response::html($xml)->withHeader('Content-Type', 'application/xml; charset=utf-8');
    }
}

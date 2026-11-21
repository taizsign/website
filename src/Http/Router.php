<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Routeur minimaliste : associe « méthode + motif d'URL » à une action de contrôleur.
 * Les paramètres s'écrivent {nom} et acceptent [a-z0-9-].
 *
 * Chaque route existe dans chaque langue du site : la langue par défaut n'a pas de préfixe,
 * les autres en ont un (/en/…) et peuvent traduire le chemin (/projets devient /en/projects).
 */
final class Router
{
    /** @var list<array{method: string, name: string, action: array{class-string, string}, patterns: array<string, string>, regexes: array<string, string>}> */
    private array $routes = [];

    /** @var string langue utilisée quand une route n'est pas traduite */
    private readonly string $default;

    /**
     * @param array<string, string> $prefixes langue => préfixe d'URL ('' pour la langue par défaut)
     */
    public function __construct(private readonly array $prefixes = ['fr' => ''])
    {
        $this->default = (string) array_key_first($prefixes);
    }

    /**
     * @param array{class-string, string} $action
     * @param array<string, string>       $paths     chemin traduit par langue, sans le préfixe : ['en' => '/projects']
     * @param bool                        $localized false : une seule adresse pour toutes les langues (fichiers, par exemple)
     */
    public function get(string $pattern, array $action, string $name, array $paths = [], bool $localized = true): self
    {
        $patterns = [];
        foreach ($localized ? $this->prefixes : [$this->default => ''] as $locale => $prefix) {
            $patterns[$locale] = rtrim($prefix . ($paths[$locale] ?? $pattern), '/') ?: '/';
        }

        $regexes = array_map(
            static fn (string $p): string => '#^' . preg_replace('#\{([a-z]+)\}#', '(?P<$1>[a-z0-9-]+)', $p) . '$#',
            $patterns,
        );

        $this->routes[] = [
            'method'   => 'GET',
            'name'     => $name,
            'action'   => $action,
            'patterns' => $patterns,
            'regexes'  => $regexes,
        ];

        return $this;
    }

    /**
     * @return array{action: array{class-string, string}, params: array<string, string>, name: string, locale: string}|null
     */
    public function match(string $method, string $path): ?array
    {
        $method = $method === 'HEAD' ? 'GET' : $method;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            foreach ($route['regexes'] as $locale => $regex) {
                if (preg_match($regex, $path, $m)) {
                    $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);

                    return ['action' => $route['action'], 'params' => $params, 'name' => $route['name'], 'locale' => $locale];
                }
            }
        }

        return null;
    }

    /**
     * Génère l'URL d'une route nommée : $router->url('project', ['slug' => 'mediatek86'], 'en').
     *
     * @param array<string, string> $params
     */
    public function url(string $name, array $params = [], ?string $locale = null): string
    {
        foreach ($this->routes as $route) {
            if ($route['name'] === $name) {
                $pattern = $route['patterns'][$locale ?? $this->default] ?? $route['patterns'][$this->default];

                return preg_replace_callback(
                    '#\{([a-z]+)\}#',
                    static fn (array $m): string => rawurlencode($params[$m[1]] ?? ''),
                    $pattern,
                );
            }
        }

        throw new \InvalidArgumentException("Route inconnue : {$name}");
    }

    /**
     * Toutes les routes, pour la commande « routes ».
     *
     * @return list<array{name: string, patterns: array<string, string>}>
     */
    public function all(): array
    {
        return array_map(static fn (array $r): array => ['name' => $r['name'], 'patterns' => $r['patterns']], $this->routes);
    }
}

<?php

declare(strict_types=1);

namespace App\View;

use App\Domain\CodeExcerpt;
use App\Domain\Topology;
use App\Http\Router;
use App\I18n\Translator;

/**
 * Moteur de gabarits en PHP natif.
 * Dans un gabarit, $this désigne la vue : $this->e(), $this->t(), $this->url(), $this->asset()…
 */
final class View
{
    /** @var array<string, mixed> */
    private array $shared = [];

    public function __construct(
        private readonly string $templateDir,
        private readonly string $publicDir,
        private readonly Router $router,
        private readonly Translator $translator,
    ) {
    }

    public function share(string $key, mixed $value): void
    {
        $this->shared[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->shared[$key] ?? $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = [], ?string $layout = 'layout'): string
    {
        $content = $this->partial($template, $data);

        return $layout === null ? $content : $this->partial($layout, $data + ['content' => $content]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function partial(string $template, array $data = []): string
    {
        $file = $this->templateDir . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Gabarit introuvable : {$template}");
        }

        extract($this->shared + $data, EXTR_SKIP);

        ob_start();
        try {
            include $file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return (string) ob_get_clean();
    }

    // Aides utilisées dans les gabarits
    public function e(string|int|float|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Texte de l'interface dans la langue de la page. Le texte français sert de clé
     * (voir lang/en.php) et peut contenir du HTML écrit à la main : il est affiché tel quel.
     * Les {paramètres}, eux, sont échappés.
     *
     * @param array<string, string|int|float> $params
     */
    public function t(string $text, array $params = []): string
    {
        return $this->translator->t($text, array_map(fn ($v): string => $this->e($v), $params));
    }

    public function locale(): string
    {
        return $this->translator->locale;
    }

    /**
     * Adresse d'une page, dans la langue de la page sauf mention contraire.
     *
     * @param array<string, string>      $params
     * @param array<string, string|null> $query
     */
    public function url(string $name, array $params = [], array $query = [], ?string $locale = null): string
    {
        $url = $this->router->url($name, $params, $locale ?? $this->locale());
        $query = array_filter($query, static fn ($v): bool => $v !== null);

        return $query === [] ? $url : $url . '?' . http_build_query($query, encoding_type: PHP_QUERY_RFC3986);
    }

    /**
     * La page courante dans chaque langue, pour les drapeaux et les balises hreflang.
     * Une page d'erreur renvoie vers l'accueil de l'autre langue.
     *
     * @return array<string, array{url: string, name: string, switch: string, tag: string, current: bool}>
     */
    public function alternates(): array
    {
        $route = (string) $this->get('route', '');
        $params = (array) $this->get('params', []);
        $links = [];

        foreach (Translator::LOCALES as $locale => $info) {
            try {
                $url = $this->router->url($route, $params, $locale);
            } catch (\InvalidArgumentException) {
                $url = $this->router->url('home', [], $locale);
            }
            $links[$locale] = $info + ['url' => $url, 'current' => $locale === $this->locale()];
        }

        return $links;
    }

    /**
     * URL d'un fichier statique, avec sa date de modification pour casser le cache navigateur.
     */
    public function asset(string $path): string
    {
        $file = $this->publicDir . '/assets/' . $path;
        $version = is_file($file) ? substr(md5((string) filemtime($file)), 0, 8) : '0';

        return '/assets/' . $path . '?v=' . $version;
    }

    public function isCurrent(string $section): bool
    {
        return str_starts_with((string) $this->get('route', ''), $section);
    }

    /**
     * Intitulé de section façon sommaire : « Titre ········· information ».
     * L'information de droite est une donnée réelle (un nombre, une date), pas un numéro.
     */
    public function rule(string $title, ?string $meta = null, bool $dark = false, bool $reveal = true): string
    {
        return sprintf(
            '<p class="rule%s"%s><span class="rule__title">%s</span><span class="rule__dots" aria-hidden="true"></span>%s</p>',
            $dark ? ' rule--dark' : '',
            $reveal ? ' data-reveal' : '',
            $this->e($title),
            $meta === null ? '' : '<span class="rule__meta">' . $this->e($meta) . '</span>',
        );
    }

    /**
     * Note sur 5 en étoiles. Chaque étoile porte son rang (--n) pour l'animation en cascade.
     */
    public function stars(int $level, int $max = 5): string
    {
        $html = '';
        for ($n = 1; $n <= $max; $n++) {
            $html .= sprintf('<i class="star%s" style="--n: %d"></i>', $n <= $level ? ' is-on' : '', $n);
        }

        return sprintf('<span class="stars" role="img" aria-label="%s">%s</span>', $this->t('{n} sur {max}', ['n' => $level, 'max' => $max]), $html);
    }

    /**
     * « 1 dossier », « 2 dossiers ». Les deux formes françaises sont des clés de traduction.
     */
    public function plural(int $count, string $singular, ?string $plural = null): string
    {
        return $count . "\u{00A0}" . $this->t($count > 1 ? ($plural ?? $singular . 's') : $singular);
    }

    /**
     * Nom propre long (« MediaTekDocuments ») : autorise une coupure aux majuscules
     * internes plutôt qu'au milieu d'un mot sur petit écran.
     */
    public function techLogo(string $tech): string
    {
        static $map = [
            'c#' => 'csharp', '.net' => 'csharp', 'winforms' => 'windows', 'visual studio' => 'windows',
            'newtonsoft' => 'api', 'postman' => 'api', 'api' => 'api',
            'mstest' => 'tests', 'phpunit' => 'tests', 'sonarlint' => 'tests',
            'phpdocumentor' => 'php', 'php' => 'php', 'composer' => 'php', 'pdo' => 'dba',
            'mysql' => 'mysql', 'sqlite' => 'sqlite', 'postgresql' => 'postgresql',
            'vps' => 'server', 'winscp' => 'server', 'ubuntu' => 'linux', 'linux' => 'linux', 'nginx' => 'nginx',
            'github actions' => 'githubactions', 'git' => 'git', 'docker' => 'docker',
            'symfony' => 'symfony', 'doctrine' => 'doctrine', 'twig' => 'twig',
            'javascript' => 'javascript', 'python' => 'python', 'powershell' => 'powershell',
        ];
        $name = mb_strtolower($tech);
        foreach ($map as $prefix => $file) {
            if (str_starts_with($name, $prefix)) {
                return $this->asset('img/logos/' . $file . '.svg');
            }
        }

        return $this->asset('img/logos/code.svg');
    }

    /**
     * Toutes les technologies d'un projet, à plat et sans doublon.
     *
     * @return list<string>
     */
    public function techList(\App\Domain\Project $project): array
    {
        return array_values(array_unique(array_merge(...array_values($project->stack ?: [[]]))));
    }

    public function breakable(string $name): string
    {
        return (string) preg_replace('/(?<=\p{Ll})(?=\p{Lu})/u', '<wbr>', $this->e($name));
    }

    /**
     * Texte avec paragraphes, `code` et **gras**.
     */
    public function text(string $text): string
    {
        return Text::paragraphs($text);
    }

    /**
     * Texte sur une ligne avec `code` et **gras**.
     */
    public function inline(string $text): string
    {
        return Text::inline($text);
    }

    public function code(CodeExcerpt $excerpt): string
    {
        return Highlighter::highlight($excerpt->code, $excerpt->language);
    }

    public function topology(Topology $topology, string $id): string
    {
        return (new TopologyRenderer($topology, $this->translator->t(...)))->render($id);
    }

    /**
     * @param list<\App\Domain\Milestone> $milestones
     */
    public function journeyProfile(array $milestones, string $cap): string
    {
        return (new JourneyProfile($milestones, JourneyProfile::today(), $cap, $this->translator->t(...)))->render();
    }

    /**
     * Nombre à la française (1 234,5) ou à l'anglaise (1,234.5).
     */
    public function number(float|int $value, int $decimals = 0): string
    {
        return $this->locale() === 'fr'
            ? number_format($value, $decimals, ',', "\u{202F}")
            : number_format($value, $decimals, '.', ',');
    }
}

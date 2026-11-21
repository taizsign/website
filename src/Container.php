<?php

declare(strict_types=1);

namespace App;

use App\Database\Database;
use App\Database\SchemaBuilder;
use App\Http\Router;
use App\I18n\Translator;
use App\Repository\ContentRepository;
use App\Repository\ProjectRepository;
use App\Service\FeedReader;
use App\Service\Uptime;
use App\View\View;

/**
 * Conteneur de services : chaque service est créé à la première demande,
 * puis réutilisé. Une page qui n'a pas besoin de la sonde HTTP ne la crée jamais.
 */
final class Container
{
    /** @var array<string, object> */
    private array $instances = [];

    private string $locale = Translator::DEFAULT;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        public readonly string $root,
        public readonly array $config,
        public readonly Router $router,
    ) {
    }

    /**
     * Langue de la requête. À fixer avant de créer la vue ou la base.
     */
    public function useLocale(string $locale): void
    {
        if (isset($this->instances['view']) || isset($this->instances['translator'])) {
            throw new \LogicException('La langue doit être choisie avant de créer la vue.');
        }
        $this->locale = $locale;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function translator(): Translator
    {
        return $this->once(__FUNCTION__, fn (): Translator => new Translator($this->locale, $this->root . '/lang'));
    }

    /**
     * Une base par langue : même schéma, contenu traduit (database/content/<langue>/).
     */
    public function db(?string $locale = null): Database
    {
        $locale ??= $this->locale;

        return $this->once('db.' . $locale, fn (): Database => self::schema($this->root, $locale)->open());
    }

    public static function schema(string $root, string $locale): SchemaBuilder
    {
        return new SchemaBuilder($root . '/storage/portfolio.' . $locale . '.sqlite', $root . '/database', $locale);
    }

    public function view(): View
    {
        return $this->once(__FUNCTION__, fn (): View => new View(
            $this->root . '/templates',
            $this->root . '/public',
            $this->router,
            $this->translator(),
        ));
    }

    public function projects(): ProjectRepository
    {
        return $this->once(__FUNCTION__, fn (): ProjectRepository => new ProjectRepository($this->db()));
    }

    public function content(): ContentRepository
    {
        return $this->once(__FUNCTION__, fn (): ContentRepository => new ContentRepository($this->db()));
    }

    /**
     * Les relevés de la sonde vivent dans la base française, quelle que soit la langue affichée.
     */
    public function uptime(): Uptime
    {
        return $this->once(__FUNCTION__, fn (): Uptime => new Uptime($this->db(Translator::DEFAULT), $this->config['uptime']));
    }

    public function feeds(): FeedReader
    {
        return $this->once(__FUNCTION__, fn (): FeedReader => new FeedReader($this->root . '/storage/cache'));
    }

    /**
     * @template T of object
     * @param callable(): T $factory
     * @return T
     */
    private function once(string $name, callable $factory): object
    {
        return $this->instances[$name] ??= $factory();
    }
}

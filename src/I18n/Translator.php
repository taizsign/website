<?php

declare(strict_types=1);

namespace App\I18n;

/**
 * Traductions de l'interface.
 *
 * Le français est la langue source : dans les gabarits, on écrit le texte en français
 * et on le passe à $this->t(). Pour une autre langue, ce texte sert de clé dans
 * lang/<langue>.php. Une clé absente retombe sur le français plutôt que de laisser un trou ;
 * « php bin/console i18n:check » liste celles qui manquent.
 */
final class Translator
{
    /** Langues du site, la première est celle par défaut (sans préfixe d'URL). */
    public const LOCALES = [
        'fr' => ['name' => 'Français', 'tag' => 'fr_FR', 'switch' => 'Version française'],
        'en' => ['name' => 'English',  'tag' => 'en_GB', 'switch' => 'English version'],
    ];
    public const DEFAULT = 'fr';

    /** @var array<string, string>|null */
    private ?array $messages = null;

    public function __construct(
        public readonly string $locale,
        private readonly string $dir,
    ) {
        if (!isset(self::LOCALES[$locale])) {
            throw new \InvalidArgumentException("Langue inconnue : {$locale}");
        }
    }

    /**
     * Texte traduit. Les {paramètres} sont remplacés tels quels : à l'appelant de les échapper.
     *
     * @param array<string, string|int|float> $params
     */
    public function t(string $text, array $params = []): string
    {
        if ($this->locale !== self::DEFAULT) {
            $text = $this->messages()[$text] ?? $text;
        }

        if ($params === []) {
            return $text;
        }

        $pairs = [];
        foreach ($params as $key => $value) {
            $pairs['{' . $key . '}'] = (string) $value;
        }

        return strtr($text, $pairs);
    }

    public function has(string $text): bool
    {
        return $this->locale === self::DEFAULT || isset($this->messages()[$text]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        if ($this->messages === null) {
            $file = $this->dir . '/' . $this->locale . '.php';
            $this->messages = $this->locale !== self::DEFAULT && is_file($file) ? require $file : [];
        }

        return $this->messages;
    }

    /**
     * Langue d'après le début du chemin : /en/projects donne en, tout le reste donne fr.
     */
    public static function fromPath(string $path): string
    {
        foreach (array_keys(self::LOCALES) as $locale) {
            if ($locale !== self::DEFAULT && ($path === '/' . $locale || str_starts_with($path, '/' . $locale . '/'))) {
                return $locale;
            }
        }

        return self::DEFAULT;
    }

    /**
     * Préfixe d'URL de chaque langue : '' pour la langue par défaut, '/en' pour l'anglais.
     *
     * @return array<string, string>
     */
    public static function prefixes(): array
    {
        $prefixes = [];
        foreach (array_keys(self::LOCALES) as $locale) {
            $prefixes[$locale] = $locale === self::DEFAULT ? '' : '/' . $locale;
        }

        return $prefixes;
    }
}

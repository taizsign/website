<?php

declare(strict_types=1);

/*
 * Chargement automatique des classes App\* (PSR-4 : App\Http\Router -> src/Http/Router.php).
 * Si Composer a été lancé, on utilise son autoloader ; sinon ce petit chargeur suffit,
 * ce qui permet de déployer le site sur un hébergement sans Composer.
 */

$composer = dirname(__DIR__) . '/vendor/autoload.php';

if (is_file($composer)) {
    require $composer;
    return;
}

spl_autoload_register(static function (string $class): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }

    $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, 4)) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

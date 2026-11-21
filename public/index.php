<?php

declare(strict_types=1);

/*
 * Contrôleur frontal : toutes les requêtes passent par ici.
 * Avec le serveur intégré de PHP (php -S), on laisse passer les fichiers statiques,
 * mais seulement ceux qui sont vraiment dans public/ (pas de « /../config/app.php »)
 * et jamais un script PHP, que le serveur exécuterait.
 */

if (PHP_SAPI === 'cli-server') {
    $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    $file = realpath(__DIR__ . $path);
    if ($file !== false && is_file($file) && str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR)
        && strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') {
        return false;
    }
}

require dirname(__DIR__) . '/src/bootstrap.php';

App\Kernel::boot(dirname(__DIR__))
    ->handle(App\Http\Request::fromGlobals())
    ->send();

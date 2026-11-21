<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

/**
 * Sert les PDF des projets. Les fichiers sont rangés hors du dossier public :
 * seul un document déclaré en base, avec un fichier existant, peut être lu.
 */
final class DocumentController extends AbstractController
{
    public function show(Request $request, string $slug): Response
    {
        $document = $this->container->projects()->findDocument($slug);

        if ($document === null || !$document->isAvailable()) {
            return $this->notFound();
        }

        $dir = realpath($this->container->root . '/storage/documents');
        $path = realpath($dir . '/' . $document->filename);

        // Défense en profondeur : le fichier doit vraiment se trouver dans storage/documents.
        if ($dir === false || $path === false || !str_starts_with($path, $dir . DIRECTORY_SEPARATOR)) {
            return $this->notFound();
        }

        return Response::file($path, 'application/pdf', $document->filename);
    }
}

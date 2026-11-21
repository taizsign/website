<?php

declare(strict_types=1);

namespace App\Database;

/**
 * Applique la traduction d'un fichier de contenu (database/content/en/<nom>.php)
 * sur sa version française.
 *
 * Un fichier traduit ne contient que du texte : les niveaux, les dates, les liens,
 * le code et les coordonnées des schémas restent dans le fichier français, qui fait foi.
 * Ce qui n'est pas traduit garde son texte français.
 */
final class ContentTranslation
{
    public function __construct(private readonly string $dir)
    {
    }

    /**
     * @param array<mixed> $data
     * @return array<mixed>
     */
    public function apply(string $name, array $data): array
    {
        $file = $this->dir . '/' . $name . '.php';
        if (!is_file($file)) {
            return $data;
        }

        $tr = require $file;

        return match ($name) {
            'projects' => $this->projects($data, $tr),
            'skills'   => $this->skills($data, $tr),
            'journey'  => $this->journey($data, $tr),
            'lab'      => $this->lab($data, $tr),
            // bts (par code) et watch (par rubrique, dans l'ordre) : même structure, texte seul
            default    => array_replace_recursive($data, $tr),
        };
    }

    /**
     * Projets, repérés par leur slug.
     *
     * @param list<array<string, mixed>>          $projects
     * @param array<string, array<string, mixed>> $tr
     * @return list<array<string, mixed>>
     */
    private function projects(array $projects, array $tr): array
    {
        foreach ($projects as &$p) {
            $t = $tr[$p['slug']] ?? null;
            if ($t === null) {
                continue;
            }

            // Textes simples, et sections remplacées en bloc (brief, build, retro)
            foreach (['title', 'tagline', 'summary', 'context', 'role', 'architecture_caption', 'brief', 'build', 'retro'] as $key) {
                if (isset($t[$key])) {
                    $p[$key] = $t[$key];
                }
            }

            // Libellés des liens, dans l'ordre
            foreach ($t['links'] ?? [] as $i => $label) {
                if (isset($p['links'][$i])) {
                    $p['links'][$i][0] = $label;
                }
            }

            // Schéma : nœuds par identifiant, flèches « départ>arrivée », cadres par libellé
            foreach ($p['architecture']['nodes'] as &$node) {
                $node = array_replace($node, $t['nodes'][$node['id']] ?? []);
            }
            unset($node);
            foreach ($p['architecture']['links'] as &$link) {
                $link['label'] = $t['arrows'][$link['from'] . '>' . $link['to']] ?? $link['label'];
            }
            unset($link);
            foreach ($p['architecture']['groups'] ?? [] as $i => $group) {
                $p['architecture']['groups'][$i]['label'] = $t['groups'][$group['label']] ?? $group['label'];
            }

            // Extraits de code : titre et légende, dans l'ordre. Le code reste tel qu'il est dans le dépôt.
            foreach ($t['excerpts'] ?? [] as $i => $excerpt) {
                if (isset($p['excerpts'][$i])) {
                    $p['excerpts'][$i] = array_replace($p['excerpts'][$i], $excerpt);
                }
            }

            // Couches de la stack renommées, dans le même ordre (et les rares technologies dont le nom se traduit)
            $stack = [];
            foreach ($p['stack'] as $layer => $technologies) {
                $stack[$t['layers'][$layer] ?? $layer] = array_map(
                    static fn (string $tech): string => $t['technologies'][$tech] ?? $tech,
                    $technologies,
                );
            }
            $p['stack'] = $stack;

            // Documents, par slug : [libellé, précision]
            foreach ($p['documents'] as &$doc) {
                if (isset($t['documents'][$doc[0]])) {
                    [$doc[1], $doc[2]] = $t['documents'][$doc[0]];
                }
            }
            unset($doc);
        }
        unset($p);

        return $projects;
    }

    /**
     * Compétences, repérées par leur nom français : [nom, détail]. Les preuves sont traduites
     * par une table commune (les liens vers les projets gardent le nom du projet).
     *
     * @param list<array<int, mixed>> $skills
     * @param array{skills?: array<string, array{0: string, 1: string}>, proofs?: array<string, string>} $tr
     * @return list<array<int, mixed>>
     */
    private function skills(array $skills, array $tr): array
    {
        foreach ($skills as &$s) {
            [$s[0], $s[4]] = $tr['skills'][$s[0]] ?? [$s[0], $s[4]];
            $s[5] = array_map(
                static fn (string|array $proof): string|array => is_array($proof) ? $proof : ($tr['proofs'][$proof] ?? $proof),
                $s[5],
            );
        }
        unset($s);

        return $skills;
    }

    /**
     * Parcours, dans l'ordre : chaque champ traduit remplace le champ français (les étiquettes en bloc).
     *
     * @param list<array<string, mixed>> $milestones
     * @param list<array<string, mixed>> $tr
     * @return list<array<string, mixed>>
     */
    private function journey(array $milestones, array $tr): array
    {
        foreach ($milestones as $i => $m) {
            $milestones[$i] = array_replace($m, array_intersect_key($tr[$i] ?? [], array_flip(['period', 'kind', 'title', 'body', 'tags'])));
        }

        return $milestones;
    }

    /**
     * Baie du homelab, dans l'ordre : [rôle, nom, note]. Le statut reste une valeur de la base.
     *
     * @param list<array{0: int, 1: string, 2: string, 3: string, 4: string}> $units
     * @param list<array{0: string, 1: string, 2: string}>                     $tr
     * @return list<array{0: int, 1: string, 2: string, 3: string, 4: string}>
     */
    private function lab(array $units, array $tr): array
    {
        foreach ($units as $i => $u) {
            if (isset($tr[$i])) {
                [$units[$i][1], $units[$i][2], $units[$i][4]] = $tr[$i];
            }
        }

        return $units;
    }
}

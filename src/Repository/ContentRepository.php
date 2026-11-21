<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Database;
use App\Domain\LabUnit;
use App\Domain\Milestone;
use App\Domain\WatchItem;

/**
 * Contenus éditoriaux simples : parcours, veille, homelab, chiffres clés.
 */
final class ContentRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    /**
     * @return list<Milestone>
     */
    public function milestones(): array
    {
        return array_map(Milestone::fromRow(...), $this->db->all('SELECT * FROM milestone ORDER BY position'));
    }

    /**
     * @return array<string, list<WatchItem>> kind => éléments
     */
    public function watch(): array
    {
        $items = [];
        foreach ($this->db->all('SELECT * FROM watch_item ORDER BY kind, position') as $row) {
            $items[$row['kind']][] = WatchItem::fromRow($row);
        }

        return $items;
    }

    /**
     * @return list<LabUnit>
     */
    public function labUnits(): array
    {
        return array_map(LabUnit::fromRow(...), $this->db->all('SELECT * FROM lab_unit ORDER BY position'));
    }

    /**
     * Nom des projets par slug (pour transformer les preuves en liens).
     *
     * @return array<string, string>
     */
    public function projectNames(): array
    {
        $names = [];
        foreach ($this->db->all('SELECT slug, title FROM project ORDER BY position') as $r) {
            $names[$r['slug']] = $r['title'];
        }

        return $names;
    }

    /**
     * Quelques chiffres tirés de la base, pour la page d'accueil.
     *
     * @return array{skills: int, acquired: int, learning: int, goals: int, projects: int, technologies: int}
     */
    public function figures(): array
    {
        $row = $this->db->one(
            "SELECT COUNT(*) AS skills,
                    SUM(status = 'acquis')   AS acquired,
                    SUM(status = 'en cours') AS learning,
                    SUM(status = 'objectif') AS goals,
                    (SELECT COUNT(*) FROM project)    AS projects,
                    (SELECT COUNT(*) FROM technology) AS technologies
               FROM skill",
        ) ?? [];

        return array_map('intval', $row);
    }
}

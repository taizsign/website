<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Database;
use App\Domain\CodeExcerpt;
use App\Domain\Document;
use App\Domain\Link;
use App\Domain\Project;
use App\Domain\Section;

final class ProjectRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    /**
     * Tous les projets, avec leur stack (pour les listes).
     *
     * @return list<Project>
     */
    public function all(): array
    {
        $projects = array_map(
            Project::fromRow(...),
            $this->db->all('SELECT * FROM project ORDER BY position'),
        );

        $stacks = $this->stacks();

        return array_map(
            static fn (Project $p): Project => $p->with(['stack' => $stacks[$p->id] ?? []]),
            $projects,
        );
    }

    public function findBySlug(string $slug): ?Project
    {
        $row = $this->db->one('SELECT * FROM project WHERE slug = ?', [$slug]);
        if ($row === null) {
            return null;
        }

        $project = Project::fromRow($row);
        $id = $project->id;

        $links = array_map(
            static fn (array $r): Link => new Link($r['label'], $r['url']),
            $this->db->all('SELECT label, url FROM project_link WHERE project_id = ? ORDER BY position', [$id]),
        );

        $sections = [];
        foreach ($this->db->all('SELECT * FROM project_section WHERE project_id = ? ORDER BY kind, position', [$id]) as $r) {
            $sections[$r['kind']][] = new Section($r['kind'], (int) $r['position'], $r['title'], $r['body']);
        }

        $excerpts = array_map(
            static fn (array $r): CodeExcerpt => new CodeExcerpt($r['title'], $r['language'], $r['source'], $r['code'], $r['caption']),
            $this->db->all('SELECT * FROM code_excerpt WHERE project_id = ? ORDER BY position', [$id]),
        );

        $documents = array_map(
            static fn (array $r): Document => new Document($r['slug'], $r['label'], $r['meta'], $r['filename']),
            $this->db->all('SELECT * FROM document WHERE project_id = ? ORDER BY position', [$id]),
        );

        $competencies = [];
        $rows = $this->db->all(
            'SELECT b.code AS block, b.label AS block_label, c.code, c.label
               FROM project_competency pc
               JOIN bts_competency c ON c.code = pc.competency_code
               JOIN bts_block b ON b.code = c.block_code
              WHERE pc.project_id = ?
              ORDER BY c.code',
            [$id],
        );
        foreach ($rows as $r) {
            $competencies[$r['block']]['label'] = $r['block_label'];
            $competencies[$r['block']]['items'][$r['code']] = $r['label'];
        }

        return $project->with([
            'links'        => $links,
            'sections'     => $sections,
            'stack'        => $this->stacks($id)[$id] ?? [],
            'excerpts'     => $excerpts,
            'documents'    => $documents,
            'competencies' => $competencies,
        ]);
    }

    /**
     * Projet suivant dans l'ordre d'affichage (on reboucle sur le premier).
     */
    public function next(Project $current): ?Project
    {
        $row = $this->db->one('SELECT * FROM project WHERE position > ? ORDER BY position LIMIT 1', [$current->position])
            ?? $this->db->one('SELECT * FROM project WHERE id <> ? ORDER BY position LIMIT 1', [$current->id]);

        return $row === null ? null : Project::fromRow($row);
    }

    public function findDocument(string $slug): ?Document
    {
        $r = $this->db->one('SELECT * FROM document WHERE slug = ?', [$slug]);

        return $r === null ? null : new Document($r['slug'], $r['label'], $r['meta'], $r['filename']);
    }

    /**
     * @return array<int, array<string, list<string>>> project_id => couche => technologies
     */
    private function stacks(?int $projectId = null): array
    {
        $sql = 'SELECT pt.project_id, pt.layer, t.name
                  FROM project_technology pt
                  JOIN technology t ON t.id = pt.technology_id';
        $params = [];

        if ($projectId !== null) {
            $sql .= ' WHERE pt.project_id = ?';
            $params[] = $projectId;
        }

        $stacks = [];
        foreach ($this->db->all($sql . ' ORDER BY pt.project_id, pt.position', $params) as $r) {
            $stacks[(int) $r['project_id']][$r['layer']][] = $r['name'];
        }

        return $stacks;
    }
}

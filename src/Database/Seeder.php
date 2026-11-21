<?php

declare(strict_types=1);

namespace App\Database;

/**
 * Remplit une base vide à partir des fichiers de database/content/.
 * Le contenu reste éditable en PHP lisible ; la base, elle, garantit
 * les contraintes (clés étrangères, CHECK) et sert les requêtes du site.
 *
 * Pour une autre langue que le français, la traduction de database/content/<langue>/
 * est appliquée par-dessus chaque fichier avant l'insertion.
 */
final class Seeder
{
    private readonly ContentTranslation $translation;

    public function __construct(
        private readonly Database $db,
        private readonly string $contentDir,
        string $locale = 'fr',
    ) {
        $this->translation = new ContentTranslation($contentDir . '/' . $locale);
    }

    public function run(): void
    {
        $this->db->transaction(function (): void {
            $this->seedBts($this->load('bts'));
            $projectIds = $this->seedProjects($this->load('projects'));
            $this->seedSkills($this->load('skills'), $projectIds);
            $this->seedJourney($this->load('journey'));
            $this->seedWatch($this->load('watch'));
            $this->seedLab($this->load('lab'));
        });
    }

    /**
     * @return array<mixed>
     */
    private function load(string $name): array
    {
        return $this->translation->apply($name, require $this->contentDir . '/' . $name . '.php');
    }

    /**
     * @param array<string, array{label: string, items: array<string, string>}> $blocks
     */
    private function seedBts(array $blocks): void
    {
        foreach ($blocks as $code => $block) {
            $this->db->execute('INSERT INTO bts_block (code, label) VALUES (?, ?)', [$code, $block['label']]);
            foreach ($block['items'] as $itemCode => $label) {
                $this->db->execute(
                    'INSERT INTO bts_competency (code, block_code, label) VALUES (?, ?, ?)',
                    [$itemCode, $code, $label],
                );
            }
        }
    }

    /**
     * @param list<array<string, mixed>> $projects
     * @return array<string, int> slug => id
     */
    private function seedProjects(array $projects): array
    {
        $ids = [];

        foreach ($projects as $position => $p) {
            $this->db->execute(
                'INSERT INTO project (slug, position, title, tagline, summary, year, context, role, video_id, architecture, architecture_caption)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $p['slug'], $position + 1, $p['title'], $p['tagline'], $p['summary'], $p['year'],
                    $p['context'], $p['role'], $p['video'],
                    json_encode($p['architecture'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                    $p['architecture_caption'],
                ],
            );
            $id = $this->db->lastInsertId();
            $ids[$p['slug']] = $id;

            foreach ($p['links'] as $i => [$label, $url]) {
                $this->db->execute('INSERT INTO project_link VALUES (?, ?, ?, ?)', [$id, $i + 1, $label, $url]);
            }

            foreach (['brief', 'build', 'retro'] as $kind) {
                foreach ($p[$kind] as $i => [$title, $body]) {
                    $this->db->execute(
                        'INSERT INTO project_section (project_id, kind, position, title, body) VALUES (?, ?, ?, ?, ?)',
                        [$id, $kind, $i + 1, $title, $body],
                    );
                }
            }

            $techPosition = 0;
            foreach ($p['stack'] as $layer => $technologies) {
                foreach ($technologies as $name) {
                    $this->db->execute('INSERT OR IGNORE INTO technology (name) VALUES (?)', [$name]);
                    $this->db->execute(
                        'INSERT INTO project_technology (project_id, technology_id, layer, position)
                         SELECT ?, id, ?, ? FROM technology WHERE name = ?',
                        [$id, $layer, ++$techPosition, $name],
                    );
                }
            }

            foreach ($p['excerpts'] as $i => $e) {
                $this->db->execute(
                    'INSERT INTO code_excerpt (project_id, position, title, language, source, code, caption) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [$id, $i + 1, $e['title'], $e['language'], $e['source'], $e['code'], $e['caption']],
                );
            }

            foreach ($p['documents'] as $i => [$slug, $label, $meta, $file]) {
                $this->db->execute(
                    'INSERT INTO document (slug, project_id, position, label, meta, filename) VALUES (?, ?, ?, ?, ?, ?)',
                    [$slug, $id, $i + 1, $label, $meta, $file],
                );
            }

            foreach ($p['competencies'] as $code) {
                $this->db->execute('INSERT INTO project_competency VALUES (?, ?)', [$id, $code]);
            }
        }

        return $ids;
    }

    /**
     * @param list<array{0: string, 1: string, 2: int, 3: string, 4: string, 5: list<string|array{0: string, 1: string}>, 6: string}> $skills
     * @param array<string, int> $projectIds
     */
    private function seedSkills(array $skills, array $projectIds): void
    {
        foreach ($skills as [$name, $domain, $level, $status, $detail, $proofs, $logo]) {
            $this->db->execute(
                'INSERT INTO skill (name, domain, level, status, detail, logo) VALUES (?, ?, ?, ?, ?, ?)',
                [$name, $domain, $level, $status, $detail, $logo],
            );
            $skillId = $this->db->lastInsertId();

            foreach ($proofs as $i => $proof) {
                [$label, $slug] = is_array($proof) ? $proof : [$proof, null];
                $this->db->execute(
                    'INSERT INTO skill_proof (skill_id, position, label, project_id) VALUES (?, ?, ?, ?)',
                    [$skillId, $i + 1, $label, $slug !== null ? ($projectIds[$slug] ?? null) : null],
                );
            }
        }
    }

    /**
     * @param list<array<string, mixed>> $milestones
     */
    private function seedJourney(array $milestones): void
    {
        foreach ($milestones as $i => $m) {
            $this->db->execute(
                'INSERT INTO milestone (position, period, kind, title, body, tags, state, at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$i + 1, $m['period'], $m['kind'], $m['title'], $m['body'], implode('|', $m['tags']), $m['state'], $m['at']],
            );
        }
    }

    /**
     * @param array<string, list<list<string>>> $watch
     */
    private function seedWatch(array $watch): void
    {
        foreach ($watch as $kind => $items) {
            foreach ($items as $i => $item) {
                $this->db->execute(
                    'INSERT INTO watch_item (kind, position, title, body, meta, url) VALUES (?, ?, ?, ?, ?, ?)',
                    [$kind, $i + 1, $item[0], $item[1], $item[2] ?? '', $item[3] ?? null],
                );
            }
        }
    }

    /**
     * @param list<array{0: int, 1: string, 2: string, 3: string, 4: string}> $units
     */
    private function seedLab(array $units): void
    {
        foreach ($units as $i => [$height, $role, $name, $status, $note]) {
            $this->db->execute(
                'INSERT INTO lab_unit (position, height, role, name, status, note) VALUES (?, ?, ?, ?, ?, ?)',
                [$i + 1, $height, $role, $name, $status, $note],
            );
        }
    }
}

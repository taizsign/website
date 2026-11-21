<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Un projet. Les relations (sections, stack, extraits…) ne sont chargées
 * que pour la page de détail ; la liste se contente des champs principaux.
 */
final class Project
{
    /**
     * @param list<Link>                                $links
     * @param array<string, list<Section>>              $sections   brief | build | retro
     * @param array<string, list<string>>               $stack      couche => technologies
     * @param list<CodeExcerpt>                         $excerpts
     * @param list<Document>                            $documents
     * @param array<string, array{label: string, items: array<string, string>}> $competencies
     */
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly int $position,
        public readonly string $title,
        public readonly string $tagline,
        public readonly string $summary,
        public readonly int $year,
        public readonly string $context,
        public readonly string $role,
        public readonly ?string $videoId,
        public readonly Topology $architecture,
        public readonly string $architectureCaption,
        public readonly array $links = [],
        public readonly array $sections = [],
        public readonly array $stack = [],
        public readonly array $excerpts = [],
        public readonly array $documents = [],
        public readonly array $competencies = [],
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            slug: $row['slug'],
            position: (int) $row['position'],
            title: $row['title'],
            tagline: $row['tagline'],
            summary: $row['summary'],
            year: (int) $row['year'],
            context: $row['context'],
            role: $row['role'],
            videoId: $row['video_id'],
            architecture: Topology::fromJson($row['architecture']),
            architectureCaption: $row['architecture_caption'],
        );
    }

    /**
     * Copie enrichie des relations (le projet reste immuable).
     *
     * @param array<string, mixed> $relations
     */
    public function with(array $relations): self
    {
        $values = get_object_vars($this);

        return new self(...array_merge($values, $relations));
    }

    public function number(): string
    {
        return str_pad((string) $this->position, 2, '0', STR_PAD_LEFT);
    }

    /**
     * @return list<Section>
     */
    public function sections(string $kind): array
    {
        return $this->sections[$kind] ?? [];
    }

    /**
     * Technologies principales, pour les listes (premières de chaque couche).
     *
     * @return list<string>
     */
    public function headlineStack(int $max = 4): array
    {
        $first = array_map(static fn (array $techs): string => $techs[0], array_filter($this->stack));

        return array_slice(array_values($first), 0, $max);
    }
}

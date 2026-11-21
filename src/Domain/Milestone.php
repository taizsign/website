<?php

declare(strict_types=1);

namespace App\Domain;

final class Milestone
{
    /**
     * @param list<string> $tags
     */
    public function __construct(
        public readonly string $period,
        public readonly string $kind,
        public readonly string $title,
        public readonly string $body,
        public readonly array $tags,
        public readonly string $state,
        public readonly float $at,
    ) {
    }

    public function stateLabel(): string
    {
        return match ($this->state) {
            'done'    => 'terminé',
            'current' => 'en cours',
            default   => 'en projet',
        };
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            $row['period'],
            $row['kind'],
            $row['title'],
            $row['body'],
            $row['tags'] === '' ? [] : explode('|', $row['tags']),
            $row['state'],
            (float) $row['at'],
        );
    }
}

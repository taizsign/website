<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Un élément de la baie du homelab (hauteur exprimée en U).
 */
final class LabUnit
{
    public function __construct(
        public readonly int $height,
        public readonly string $role,
        public readonly string $name,
        public readonly string $status,
        public readonly string $note,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self((int) $row['height'], $row['role'], $row['name'], $row['status'], $row['note']);
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            'en place' => 'is-up',
            'en cours' => 'is-building',
            default    => 'is-planned',
        };
    }
}

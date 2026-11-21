<?php

declare(strict_types=1);

namespace App\Domain;

final class WatchItem
{
    public function __construct(
        public readonly string $kind,
        public readonly string $title,
        public readonly string $body,
        public readonly string $meta,
        public readonly ?string $url,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self($row['kind'], $row['title'], $row['body'], $row['meta'], $row['url']);
    }

    /**
     * Pour les événements, meta = « 2024 · MARS · AGENT ».
     *
     * @return array{year: string, month: string, tag: string}
     */
    public function eventParts(): array
    {
        $parts = array_map('trim', explode('·', $this->meta)) + ['', '', ''];

        return ['year' => $parts[0], 'month' => $parts[1], 'tag' => $parts[2]];
    }
}

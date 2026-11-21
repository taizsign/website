<?php

declare(strict_types=1);

namespace App\Domain;

final class Document
{
    public function __construct(
        public readonly string $slug,
        public readonly string $label,
        public readonly string $meta,
        public readonly ?string $filename,
    ) {
    }

    public function isAvailable(): bool
    {
        return $this->filename !== null;
    }
}

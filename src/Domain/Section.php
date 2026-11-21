<?php

declare(strict_types=1);

namespace App\Domain;

final class Section
{
    public function __construct(
        public readonly string $kind,
        public readonly int $position,
        public readonly string $title,
        public readonly string $body,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Domain;

final class CodeExcerpt
{
    public function __construct(
        public readonly string $title,
        public readonly string $language,
        public readonly string $source,
        public readonly string $code,
        public readonly string $caption,
    ) {
    }
}

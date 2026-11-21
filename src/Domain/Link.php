<?php

declare(strict_types=1);

namespace App\Domain;

final class Link
{
    public function __construct(
        public readonly string $label,
        public readonly string $url,
    ) {
    }

    /**
     * Adresse lisible : « github.com/taizsign/mediatek86 ».
     */
    public function display(): string
    {
        return rtrim((string) preg_replace('#^https?://(www\.)?#', '', $this->url), '/');
    }
}

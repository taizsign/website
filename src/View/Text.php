<?php

declare(strict_types=1);

namespace App\View;

/**
 * Mise en forme minimale des textes stockés en base.
 * Tout est échappé d'abord : seuls `code` et **gras** deviennent du HTML.
 */
final class Text
{
    public static function inline(string $text): string
    {
        $html = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $html = preg_replace('/`([^`]+)`/u', '<code>$1</code>', $html);
        $html = preg_replace('/\*\*([^*]+)\*\*/u', '<strong>$1</strong>', $html);

        // Espaces insécables avant la ponctuation haute, à la française.
        return preg_replace('/ ([:;?!»])/u', "\u{202F}$1", $html);
    }

    public static function paragraphs(string $text): string
    {
        $blocks = preg_split('/\R{2,}/u', trim($text)) ?: [];

        return implode('', array_map(
            static fn (string $p): string => '<p>' . self::inline(trim($p)) . '</p>',
            $blocks,
        ));
    }
}

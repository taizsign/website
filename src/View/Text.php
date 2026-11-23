<?php

declare(strict_types=1);

namespace App\View;

/**
 * Mise en forme minimale des textes stockés en base.
 * Tout est échappé d'abord : seuls `code`, **gras**, ==surligné== et __souligné__ deviennent du HTML.
 */
final class Text
{
    public static function inline(string $text): string
    {
        $html = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // le code d'abord, mis de côté pour que ses caractères ne soient pas interprétés
        $codes = [];
        $html = preg_replace_callback('/`([^`]+)`/u', static function (array $m) use (&$codes): string {
            $codes[] = '<code>' . $m[1] . '</code>';
            return "\u{E000}" . (count($codes) - 1) . "\u{E001}";
        }, $html);
        $html = preg_replace('/\*\*([^*]+)\*\*/u', '<strong>$1</strong>', $html);
        $html = preg_replace('/==(.+?)==/u', '<mark>$1</mark>', $html);
        $html = preg_replace('/__(.+?)__/u', '<span class="ul">$1</span>', $html);
        $html = preg_replace_callback('/\x{E000}(\d+)\x{E001}/u', static fn (array $m): string => $codes[(int) $m[1]], $html);

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

<?php

declare(strict_types=1);

namespace App\View;

/**
 * Coloration syntaxique côté serveur, volontairement simple :
 * un découpage en jetons par expression régulière, puis un <span> par type.
 * Pas de JavaScript, pas de bibliothèque, et le code reste lisible sans CSS.
 */
final class Highlighter
{
    /** @var array<string, list<string>> */
    private const KEYWORDS = [
        'sql' => ['SELECT', 'FROM', 'WHERE', 'AND', 'OR', 'JOIN', 'LEFT', 'ON', 'AS', 'ORDER', 'BY', 'GROUP', 'BETWEEN',
            'INTERVAL', 'DAY', 'ASC', 'DESC', 'IN', 'NULL', 'CASE', 'WHEN', 'THEN', 'ELSE', 'END', 'COUNT', 'INSERT',
            'INTO', 'VALUES', 'UPDATE', 'SET', 'DELETE', 'LIMIT', 'CURDATE', 'DATE_ADD', 'NOT', 'COLLATE', 'NOCASE'],
        'csharp' => ['if', 'else', 'return', 'new', 'public', 'private', 'void', 'string', 'int', 'bool', 'var',
            'class', 'using', 'namespace', 'null', 'true', 'false', 'foreach', 'in', 'readonly', 'static'],
        'php' => ['public', 'private', 'function', 'return', 'foreach', 'as', 'if', 'else', 'array', 'new', 'null',
            'true', 'false', 'static', 'self', 'fn', 'match'],
        'powershell' => ['if', 'else', 'elseif', 'function', 'param', 'return', 'foreach', 'do', 'while', 'exit'],
        'yaml' => [],
    ];

    public static function highlight(string $code, string $language): string
    {
        $patterns = [
            'comment' => $language === 'sql' ? '--[^\n]*' : ($language === 'yaml' || $language === 'powershell' ? '#[^\n]*' : '//[^\n]*'),
            'string'  => '"(?:[^"\\\\\n]|\\\\.)*"|\'(?:[^\'\\\\\n]|\\\\.)*\'',
            'secret'  => '\$\{\{[^}]*\}\}',
            'var'     => '\$[A-Za-z_][A-Za-z0-9_]*',
            'key'     => $language === 'yaml' ? '(?m:^\s*-?\s*[A-Za-z_][\w-]*(?=:))' : '(?!)',
            'number'  => '\b\d+(?:\.\d+)?\b',
            'word'    => '[A-Za-z_][A-Za-z0-9_]*',
        ];

        $regex = '~' . implode('|', array_map(
            static fn (string $name, string $p): string => "(?P<{$name}>{$p})",
            array_keys($patterns),
            $patterns,
        )) . '~u';

        $keywords = array_flip(self::KEYWORDS[$language] ?? []);
        $caseInsensitive = $language === 'sql';

        $html = '';
        $offset = 0;

        preg_match_all($regex, $code, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($matches as $match) {
            [$token, $position] = $match[0];
            $html .= self::escape(substr($code, $offset, $position - $offset));
            $offset = $position + strlen($token);

            $type = self::typeOf($match);

            if ($type === 'word') {
                $lookup = $caseInsensitive ? strtoupper($token) : $token;
                $type = isset($keywords[$lookup]) ? 'kw' : (ctype_upper($token[0]) && $language !== 'sql' ? 'type' : null);
            }

            $html .= $type === null ? self::escape($token) : '<span class="tok-' . $type . '">' . self::escape($token) . '</span>';
        }

        return $html . self::escape(substr($code, $offset));
    }

    /**
     * @param array<int|string, array{0: string, 1: int}> $match
     */
    private static function typeOf(array $match): ?string
    {
        foreach (['comment', 'string', 'secret', 'var', 'key', 'number', 'word'] as $name) {
            if (isset($match[$name]) && $match[$name][1] !== -1 && $match[$name][0] !== '') {
                return $name;
            }
        }

        return null;
    }

    private static function escape(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

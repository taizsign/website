<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Réponse HTTP. Le corps peut être une chaîne ou un fichier à streamer.
 */
final class Response
{
    /** @var array<string, string> */
    private array $headers;

    /**
     * @param array<string, string> $headers
     */
    private function __construct(
        private readonly string $body,
        private readonly int $status = 200,
        array $headers = [],
        private readonly ?string $file = null,
    ) {
        $this->headers = $headers;
    }

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    public static function json(mixed $data, int $status = 200): self
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        return new self($body, $status, ['Content-Type' => 'application/json; charset=utf-8']);
    }

    public static function file(string $path, string $mime, string $downloadName): self
    {
        return new self('', 200, [
            'Content-Type'        => $mime,
            'Content-Length'      => (string) filesize($path),
            'Content-Disposition' => 'inline; filename="' . addcslashes($downloadName, '"\\') . '"',
            'X-Robots-Tag'        => 'noindex',
            'Cache-Control'       => 'private, max-age=3600',
        ], $path);
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self('', $status, ['Location' => $location]);
    }

    public function withHeader(string $name, string $value): self
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;

        return $clone;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function body(): string
    {
        return $this->body;
    }

    /**
     * En-têtes de sécurité appliqués à toutes les réponses (doublés dans .htaccess
     * pour Apache, mais présents ici pour que le site soit protégé avec n'importe quel serveur).
     */
    public function withSecurityHeaders(): self
    {
        $clone = clone $this;
        $clone->headers += [
            'X-Content-Type-Options'  => 'nosniff',
            'X-Frame-Options'         => 'DENY',
            'Referrer-Policy'         => 'strict-origin-when-cross-origin',
            'Permissions-Policy'      => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Content-Security-Policy' => implode('; ', [
                "default-src 'self'",
                "script-src 'self'",
                "style-src 'self' 'unsafe-inline'",
                "img-src 'self' data:",
                "font-src 'self'",
                "connect-src 'self'",
                'frame-src https://www.youtube-nocookie.com',
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
            ]),
        ];

        return $clone;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            header_remove('X-Powered-By');
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }

        if ($this->file !== null) {
            readfile($this->file);
            return;
        }

        echo $this->body;
    }
}

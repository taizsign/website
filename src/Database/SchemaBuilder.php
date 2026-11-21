<?php

declare(strict_types=1);

namespace App\Database;

/**
 * Ouvre la base SQLite d'une langue, et la reconstruit si le schéma ou le contenu a changé.
 *
 * Une empreinte (hash) du schéma, des fichiers de contenu et du Seeder est stockée
 * dans la table `meta`. Si elle ne correspond plus, la base est régénérée dans un
 * fichier temporaire puis échangée ; les relevés de disponibilité sont conservés.
 */
final class SchemaBuilder
{
    public function __construct(
        private readonly string $file,
        private readonly string $databaseDir,
        private readonly string $locale = 'fr',
    ) {
    }

    public function file(): string
    {
        return $this->file;
    }

    public function open(): Database
    {
        $fingerprint = $this->fingerprint();

        $db = $this->openIfUpToDate($fingerprint);
        if ($db !== null) {
            return $db;
        }

        $lock = fopen($this->file . '.lock', 'c');
        flock($lock, LOCK_EX);

        try {
            // Une autre requête a peut-être reconstruit la base pendant qu'on attendait le verrou.
            if ($this->openIfUpToDate($fingerprint) === null) {
                $this->rebuild($fingerprint);
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }

        return Database::sqlite($this->file);
    }

    public function rebuild(?string $fingerprint = null): void
    {
        $fingerprint ??= $this->fingerprint();
        $probes = $this->savedProbes();

        $tmp = $this->file . '.build';
        if (is_file($tmp)) {
            unlink($tmp);
        }

        $db = Database::sqlite($tmp);
        $db->script((string) file_get_contents($this->databaseDir . '/schema.sql'));
        (new Seeder($db, $this->databaseDir . '/content', $this->locale))->run();

        $db->transaction(static function (Database $db) use ($probes, $fingerprint): void {
            foreach ($probes as $p) {
                $db->execute(
                    'INSERT INTO probe (target, checked_at, http_code, ms, ok) VALUES (?, ?, ?, ?, ?)',
                    [$p['target'], $p['checked_at'], $p['http_code'], $p['ms'], $p['ok']],
                );
            }
            $db->execute("INSERT INTO meta (key, value) VALUES ('fingerprint', ?), ('built_at', ?)", [
                $fingerprint,
                date(DATE_ATOM),
            ]);
        });

        unset($db);
        gc_collect_cycles();

        if (!rename($tmp, $this->file)) {
            throw new \RuntimeException('Impossible de remplacer la base ' . $this->file);
        }
    }

    private function openIfUpToDate(string $fingerprint): ?Database
    {
        if (!is_file($this->file)) {
            return null;
        }

        try {
            $db = Database::sqlite($this->file);
            $current = $db->value("SELECT value FROM meta WHERE key = 'fingerprint'");
        } catch (\PDOException) {
            return null;
        }

        return $current === $fingerprint ? $db : null;
    }

    private function fingerprint(): string
    {
        $files = [$this->databaseDir . '/schema.sql', __DIR__ . '/Seeder.php', __DIR__ . '/ContentTranslation.php'];
        $files = [...$files, ...glob($this->databaseDir . '/content/*.php') ?: []];
        $files = [...$files, ...glob($this->databaseDir . '/content/' . $this->locale . '/*.php') ?: []];
        sort($files);

        return hash('xxh128', $this->locale . implode('', array_map(static fn (string $f): string => (string) md5_file($f), $files)));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function savedProbes(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        try {
            return Database::sqlite($this->file)->all(
                'SELECT target, checked_at, http_code, ms, ok FROM probe ORDER BY checked_at DESC LIMIT 500',
            );
        } catch (\PDOException) {
            return [];
        }
    }
}

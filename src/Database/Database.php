<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOStatement;

/**
 * Enveloppe PDO : uniquement des requêtes préparées.
 */
final class Database
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public static function sqlite(string $file): self
    {
        $pdo = new PDO('sqlite:' . $file, options: [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA busy_timeout = 3000');

        return new self($pdo);
    }

    /**
     * @param array<string|int, scalar|null> $params
     * @return list<array<string, mixed>>
     */
    public function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /**
     * @param array<string|int, scalar|null> $params
     * @return array<string, mixed>|null
     */
    public function one(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @param array<string|int, scalar|null> $params
     */
    public function value(string $sql, array $params = []): mixed
    {
        $value = $this->run($sql, $params)->fetchColumn();

        return $value === false ? null : $value;
    }

    /**
     * @param array<string|int, scalar|null> $params
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->run($sql, $params)->rowCount();
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Exécute un script SQL brut (schéma). Réservé aux fichiers du projet, jamais à une saisie.
     */
    public function script(string $sql): void
    {
        $this->pdo->exec($sql);
    }

    /**
     * @template T
     * @param callable(self): T $work
     * @return T
     */
    public function transaction(callable $work): mixed
    {
        $this->pdo->beginTransaction();

        try {
            $result = $work($this);
            $this->pdo->commit();

            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function engine(): string
    {
        return 'SQLite ' . $this->pdo->query('SELECT sqlite_version()')->fetchColumn();
    }

    /**
     * @param array<string|int, scalar|null> $params
     */
    private function run(string $sql, array $params): PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement;
    }
}

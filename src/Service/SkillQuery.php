<?php

declare(strict_types=1);

namespace App\Service;

use App\Database\Database;
use App\Http\Request;

/**
 * La « console » de la page Compétences.
 *
 * Le visiteur ne tape pas de SQL : il active ou désactive des valeurs dans une
 * requête affichée à l'écran. Chaque valeur passe par une liste blanche, la requête
 * est exécutée avec des paramètres liés, et le texte affiché est exactement celui
 * envoyé au moteur (aux paramètres près, remplacés par leurs valeurs pour la lecture).
 *
 * En anglais, la requête interroge la vue `skills` (colonnes et valeurs en anglais)
 * au lieu de `competence` : là encore, ce qui s'affiche est ce qui s'exécute.
 */
final class SkillQuery
{
    /** Valeurs telles qu'elles sont stockées dans la table skill. */
    public const DOMAINS = ['systèmes', 'réseaux', 'bdd', 'dev', 'outils'];
    public const STATUSES = ['acquis', 'en cours', 'objectif'];
    public const ORDERS = ['niveau', 'statut', 'domaine', 'nom'];

    /** Colonnes renvoyées, dans l'ordre du SELECT. */
    private const COLUMNS = ['name', 'domain', 'level', 'status', 'proof', 'detail', 'projects', 'logo'];

    /**
     * Vocabulaire de la requête, par langue : vue interrogée, nom des colonnes,
     * valeurs telles qu'elles s'écrivent dans le SQL, paramètres d'URL.
     */
    private const DIALECTS = [
        'fr' => [
            'view'     => 'competence',
            'columns'  => ['name' => 'nom', 'domain' => 'domaine', 'level' => 'niveau', 'status' => 'statut', 'proof' => 'preuve', 'detail' => 'detail', 'projects' => 'projets', 'logo' => 'logo'],
            'domains'  => ['systèmes' => 'systèmes', 'réseaux' => 'réseaux', 'bdd' => 'bdd', 'dev' => 'dev', 'outils' => 'outils'],
            'statuses' => ['acquis' => 'acquis', 'en cours' => 'en cours', 'objectif' => 'objectif'],
            'orders'   => ['niveau' => 'niveau', 'statut' => 'statut', 'domaine' => 'domaine', 'nom' => 'nom'],
            'params'   => ['domain' => 'domaine', 'status' => 'statut', 'order' => 'tri'],
        ],
        'en' => [
            'view'     => 'skills',
            'columns'  => ['name' => 'name', 'domain' => 'domain', 'level' => 'level', 'status' => 'status', 'proof' => 'proof', 'detail' => 'detail', 'projects' => 'projects', 'logo' => 'logo'],
            'domains'  => ['systèmes' => 'systems', 'réseaux' => 'networks', 'bdd' => 'db', 'dev' => 'dev', 'outils' => 'tools'],
            'statuses' => ['acquis' => 'acquired', 'en cours' => 'learning', 'objectif' => 'goal'],
            'orders'   => ['niveau' => 'level', 'statut' => 'status', 'domaine' => 'domain', 'nom' => 'name'],
            'params'   => ['domain' => 'domain', 'status' => 'status', 'order' => 'sort'],
        ],
    ];

    /** @var list<string> */
    public readonly array $domains;
    /** @var list<string> */
    public readonly array $statuses;
    public readonly string $order;

    /** @var array{view: string, columns: array<string, string>, domains: array<string, string>, statuses: array<string, string>, orders: array<string, string>, params: array<string, string>} */
    private readonly array $dialect;

    /**
     * @param list<string> $domains  valeurs stockées (self::DOMAINS)
     * @param list<string> $statuses valeurs stockées (self::STATUSES)
     */
    public function __construct(array $domains, array $statuses, string $order, string $locale = 'fr')
    {
        // array_intersect garde l'ordre de la liste blanche et écarte toute valeur inconnue.
        $this->domains = array_values(array_intersect(self::DOMAINS, $domains));
        $this->statuses = array_values(array_intersect(self::STATUSES, $statuses));
        $this->order = in_array($order, self::ORDERS, true) ? $order : 'niveau';
        $this->dialect = self::DIALECTS[$locale] ?? self::DIALECTS['fr'];
    }

    /**
     * Requête décrite par l'URL (?domaine=…&statut=…&tri=… ou ?domain=…&status=…&sort=…).
     * Sans paramètre : tout afficher.
     */
    public static function fromRequest(Request $request, string $locale): self
    {
        $d = self::DIALECTS[$locale] ?? self::DIALECTS['fr'];

        // valeurs écrites dans la langue de la page, ramenées aux valeurs stockées
        $read = static function (string $param, array $values) use ($request): ?array {
            if (!$request->hasQuery($param)) {
                return null;
            }

            return array_values(array_filter(array_map(
                static fn (string $v): string|false => array_search($v, $values, true),
                $request->queryList($param),
            )));
        };

        $order = $request->query($d['params']['order']);
        $order = is_string($order) ? array_search($order, $d['orders'], true) : false;

        return new self(
            $read($d['params']['domain'], $d['domains']) ?? self::DOMAINS,
            $read($d['params']['status'], $d['statuses']) ?? self::STATUSES,
            $order === false ? 'niveau' : $order,
            $locale,
        );
    }

    /**
     * @return array{sql: string, rows: list<array<string, mixed>>, count: int, ms: float}
     */
    public function run(Database $db): array
    {
        [$sql, $params] = $this->compile();

        $start = hrtime(true);
        $raw = $db->all($sql, $params);
        $ms = (hrtime(true) - $start) / 1e6;

        // Chaque ligne est rendue avec des clés fixes et les valeurs stockées (pour le gabarit),
        // plus les valeurs telles qu'affichées dans la requête (domain_label, status_label).
        $rows = [];
        foreach ($raw as $r) {
            $row = [];
            foreach (self::COLUMNS as $column) {
                $row[$column] = $r[$this->dialect['columns'][$column]];
            }
            $row['domain_label'] = $row['domain'];
            $row['status_label'] = $row['status'];
            $row['domain'] = array_search($row['domain'], $this->dialect['domains'], true) ?: $row['domain'];
            $row['status'] = array_search($row['status'], $this->dialect['statuses'], true) ?: $row['status'];
            $row['level'] = (int) $row['level'];
            $row['projects'] = $row['projects'] === null ? [] : explode(',', $row['projects']);
            $rows[] = $row;
        }

        return ['sql' => $this->display(), 'rows' => $rows, 'count' => count($rows), 'ms' => round($ms, 3)];
    }

    /**
     * Requête paramétrée réellement exécutée.
     *
     * @return array{0: string, 1: list<string>}
     */
    public function compile(): array
    {
        $sql = implode("\n", [
            'SELECT ' . $this->selectList(),
            'FROM ' . $this->view(),
            'WHERE ' . $this->column('domain') . ' IN (' . $this->placeholders($this->domains) . ')',
            '  AND ' . $this->column('status') . ' IN (' . $this->placeholders($this->statuses) . ')',
            'ORDER BY ' . $this->orderClause($this->order),
        ]);

        $params = [
            ...array_map(fn (string $d): string => $this->dialect['domains'][$d], $this->domains),
            ...array_map(fn (string $s): string => $this->dialect['statuses'][$s], $this->statuses),
        ];

        return [$sql, $params];
    }

    /**
     * Même requête, valeurs écrites en toutes lettres, pour l'affichage.
     */
    public function display(): string
    {
        [$sql, $params] = $this->compile();

        foreach ($params as $value) {
            $sql = preg_replace('/\?/', "'" . str_replace("'", "''", $value) . "'", $sql, 1);
        }

        return $sql . ';';
    }

    /**
     * Paramètres d'URL équivalents (pour les liens sans JavaScript).
     *
     * @return array<string, string>
     */
    public function toQuery(): array
    {
        return [
            $this->param('domain') => implode(',', array_map(fn (string $d): string => $this->dialect['domains'][$d], $this->domains)),
            $this->param('status') => implode(',', array_map(fn (string $s): string => $this->dialect['statuses'][$s], $this->statuses)),
            $this->param('order')  => $this->dialect['orders'][$this->order],
        ];
    }

    // Vocabulaire, pour le gabarit de la console

    public function view(): string
    {
        return $this->dialect['view'];
    }

    public function column(string $column): string
    {
        return $this->dialect['columns'][$column];
    }

    public function selectList(): string
    {
        return implode(', ', array_map(fn (string $c): string => $this->dialect['columns'][$c], self::COLUMNS));
    }

    /** Nom du paramètre d'URL : domain, status ou order. */
    public function param(string $key): string
    {
        return $this->dialect['params'][$key];
    }

    /**
     * @return array<string, string> valeur stockée => valeur écrite dans la requête
     */
    public function domainValues(): array
    {
        return $this->dialect['domains'];
    }

    /**
     * @return array<string, string> valeur stockée => valeur écrite dans la requête
     */
    public function statusValues(): array
    {
        return $this->dialect['statuses'];
    }

    /**
     * Choix du menu ORDER BY : valeur d'URL => texte affiché.
     *
     * @return array<string, string>
     */
    public function orderOptions(): array
    {
        $c = $this->dialect['columns'];
        $s = $this->dialect['statuses'];

        return [
            $this->dialect['orders']['niveau']  => "{$c['level']} DESC, {$c['name']}",
            $this->dialect['orders']['statut']  => "{$c['status']} ({$s['acquis']} → {$s['objectif']})",
            $this->dialect['orders']['domaine'] => "{$c['domain']}, {$c['level']} DESC",
            $this->dialect['orders']['nom']     => $c['name'],
        ];
    }

    public function selectedOrder(): string
    {
        return $this->dialect['orders'][$this->order];
    }

    private function orderClause(string $order): string
    {
        $c = $this->dialect['columns'];
        $s = $this->dialect['statuses'];

        return match ($order) {
            'statut'  => "CASE {$c['status']} WHEN '{$s['acquis']}' THEN 1 WHEN '{$s['en cours']}' THEN 2 ELSE 3 END, {$c['level']} DESC",
            'domaine' => "{$c['domain']}, {$c['level']} DESC",
            'nom'     => "{$c['name']} COLLATE NOCASE",
            default   => "{$c['level']} DESC, {$c['name']}",
        };
    }

    /**
     * Une liste vide donnerait « IN () », invalide : on écrit IN (NULL), qui ne renvoie rien.
     *
     * @param list<string> $values
     */
    private function placeholders(array $values): string
    {
        return $values === [] ? 'NULL' : implode(', ', array_fill(0, count($values), '?'));
    }
}

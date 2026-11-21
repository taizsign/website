<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Schéma d'architecture d'un projet : des nœuds, des liens, des zones.
 * Repère fixe de 960 × 420, la vue le met à l'échelle.
 */
final class Topology
{
    public const WIDTH = 960;
    public const HEIGHT = 420;
    public const NODE_HEIGHT = 64;

    /**
     * @param array<string, array{id: string, x: int, y: int, w: int, label: string, sub: string, kind: string}> $nodes
     * @param list<array{from: string, to: string, label: string, flow?: bool}> $links
     * @param list<array{label: string, x: int, y: int, w: int, h: int}> $groups
     */
    public function __construct(
        public readonly array $nodes,
        public readonly array $links,
        public readonly array $groups,
    ) {
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        $nodes = [];
        foreach ($data['nodes'] as $node) {
            $nodes[$node['id']] = $node;
        }

        return new self($nodes, $data['links'] ?? [], $data['groups'] ?? []);
    }

    /**
     * Point d'ancrage d'un lien sur le bord d'un nœud, côté de l'autre nœud.
     *
     * @return array{float, float}
     */
    public function anchor(string $from, string $to): array
    {
        $a = $this->nodes[$from];
        $b = $this->nodes[$to];

        $ax = $a['x'] + $a['w'] / 2;
        $ay = $a['y'] + self::NODE_HEIGHT / 2;
        $bx = $b['x'] + $b['w'] / 2;
        $by = $b['y'] + self::NODE_HEIGHT / 2;

        $dx = $bx - $ax;
        $dy = $by - $ay;

        // On sort par le bord horizontal ou vertical selon la direction dominante.
        if (abs($dx) * self::NODE_HEIGHT > abs($dy) * $a['w']) {
            $x = $ax + ($dx > 0 ? $a['w'] / 2 : -$a['w'] / 2);
            $y = $ay + $dy * (($x - $ax) / $dx);
        } else {
            $y = $ay + ($dy > 0 ? self::NODE_HEIGHT / 2 : -self::NODE_HEIGHT / 2);
            $x = $ax + ($dy == 0 ? 0 : $dx * (($y - $ay) / $dy));
        }

        return [round($x, 1), round($y, 1)];
    }
}

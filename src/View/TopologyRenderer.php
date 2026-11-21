<?php

declare(strict_types=1);

namespace App\View;

use App\Domain\Topology;

/**
 * Dessine un schéma d'architecture en SVG, côté serveur.
 * Le schéma est complet sans JavaScript ; le script ajoute seulement
 * les paquets qui circulent le long des liens marqués « flow ».
 */
final class TopologyRenderer
{
    /** @var \Closure(string): string */
    private readonly \Closure $t;

    /**
     * @param (callable(string): string)|null $t traduction des phrases fixes
     */
    public function __construct(private readonly Topology $topology, ?callable $t = null)
    {
        $this->t = $t === null ? static fn (string $s): string => $s : $t(...);
    }

    public function render(string $id): string
    {
        $t = $this->topology;
        $h = Topology::NODE_HEIGHT;
        $svg = [];

        [$top, $height] = $this->verticalBounds();
        $svg[] = sprintf(
            '<svg class="topo" id="%s" viewBox="0 %d %d %d" role="img" aria-labelledby="%1$s-title" data-topo>',
            $this->e($id),
            $top,
            Topology::WIDTH,
            $height,
        );
        $svg[] = '<title id="' . $this->e($id) . '-title">' . $this->e($this->summary()) . '</title>';
        $svg[] = '<defs><marker id="' . $this->e($id) . '-arrow" viewBox="0 0 8 8" refX="7" refY="4" markerWidth="7" markerHeight="7" orient="auto-start-reverse">'
            . '<path d="M0,0 L8,4 L0,8 z" class="topo-arrow"/></marker></defs>';

        foreach ($t->groups as $g) {
            $svg[] = sprintf(
                '<g class="topo-group"><rect x="%d" y="%d" width="%d" height="%d"/><text x="%d" y="%d">%s</text></g>',
                $g['x'], $g['y'], $g['w'], $g['h'], $g['x'] + 14, $g['y'] + 22, $this->e(mb_strtoupper($g['label'])),
            );
        }

        foreach ($t->links as $i => $link) {
            [$x1, $y1] = $t->anchor($link['from'], $link['to']);
            [$x2, $y2] = $t->anchor($link['to'], $link['from']);

            $flow = !empty($link['flow']);
            $mx = ($x1 + $x2) / 2;
            $my = ($y1 + $y2) / 2;
            $vertical = abs($x2 - $x1) < abs($y2 - $y1);

            $svg[] = sprintf(
                '<g class="topo-link%s"><path id="%s-l%d" d="M%s,%s L%s,%s" marker-end="url(#%s-arrow)"%s/>'
                . '<text x="%s" y="%s" text-anchor="%s">%s</text></g>',
                $flow ? ' is-flow' : '',
                $this->e($id), $i, $x1, $y1, $x2, $y2, $this->e($id),
                $flow ? ' data-flow' : '',
                $vertical ? $mx + 10 : $mx,
                $vertical ? $my + 4 : $my - 10,
                $vertical ? 'start' : 'middle',
                $this->e($link['label']),
            );
        }

        foreach ($t->nodes as $n) {
            $svg[] = sprintf(
                '<g class="topo-node is-%s" data-node="%s">'
                . '<rect x="%d" y="%d" width="%d" height="%d"/>'
                . '<rect class="topo-led" x="%d" y="%d" width="6" height="6"/>'
                . '<text class="topo-label" x="%d" y="%d">%s</text>'
                . '<text class="topo-sub" x="%d" y="%d">%s</text></g>',
                $this->e($n['kind']), $this->e($n['id']),
                $n['x'], $n['y'], $n['w'], $h,
                $n['x'] + $n['w'] - 14, $n['y'] + 8,
                $n['x'] + 14, $n['y'] + 28, $this->e($n['label']),
                $n['x'] + 14, $n['y'] + 48, $this->e($n['sub']),
            );
        }

        $svg[] = '</svg>';

        return implode("\n", $svg);
    }

    /**
     * Cadrage vertical au plus près du contenu (le repère fait 420 de haut,
     * mais un schéma n'utilise pas forcément toute la hauteur).
     *
     * @return array{int, int} [haut, hauteur]
     */
    private function verticalBounds(): array
    {
        $tops = $bottoms = [];
        foreach ($this->topology->nodes as $n) {
            $tops[] = $n['y'];
            $bottoms[] = $n['y'] + Topology::NODE_HEIGHT;
        }
        foreach ($this->topology->groups as $g) {
            $tops[] = $g['y'];
            $bottoms[] = $g['y'] + $g['h'];
        }

        $top = max(0, min($tops) - 12);

        return [$top, max($bottoms) + 12 - $top];
    }

    /**
     * Description textuelle du schéma pour les lecteurs d'écran.
     */
    private function summary(): string
    {
        $parts = [];
        foreach ($this->topology->links as $link) {
            $parts[] = $this->topology->nodes[$link['from']]['label'] . ($this->t)(' vers ') . $this->topology->nodes[$link['to']]['label']
                . ($link['label'] !== '' ? ' (' . $link['label'] . ')' : '');
        }

        return ($this->t)('Schéma d\'architecture : ') . implode(', ', $parts) . '.';
    }

    private function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

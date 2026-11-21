<?php

declare(strict_types=1);

namespace App\View;

use App\Domain\Milestone;

/**
 * Le parcours dessiné comme le profil d'une étape de course :
 * l'axe horizontal est le temps, l'altitude grimpe à chaque jalon,
 * la route est pleine jusqu'à aujourd'hui puis en pointillés jusqu'au sommet (le cap).
 *
 * Rendu en SVG côté serveur ; le CSS anime le tracé, le JavaScript relie
 * les jalons aux cartes détaillées sous le profil.
 */
final class JourneyProfile
{
    private const W = 1200;
    private const H = 470;
    private const LEFT = 40;
    private const RIGHT = 36;
    private const AXIS = 420;      // altitude 0
    private const TOP = 196;       // altitude 100
    private const ROWS = [86, 146]; // deux rangées d'étiquettes, en alternance

    /** @var list<Milestone> */
    private array $stops;
    private float $x0;
    private float $x1;
    /** @var callable(float): float */
    private $base;

    /** @var \Closure(string): string */
    private readonly \Closure $t;

    /**
     * @param list<Milestone>                 $milestones
     * @param (callable(string): string)|null $t traduction des phrases fixes
     */
    public function __construct(array $milestones, private readonly float $today, private readonly string $cap, ?callable $t = null)
    {
        $this->t = $t === null ? static fn (string $s): string => $s : $t(...);

        usort($milestones, static fn (Milestone $a, Milestone $b): int => $a->at <=> $b->at);
        $this->stops = $milestones;

        $ats = array_map(static fn (Milestone $m): float => $m->at, $milestones) ?: [$today];
        $this->x0 = min(min($ats), $today) - 0.5;
        $this->x1 = max(max($ats), $today) + 1.1;

        // Points d'appui : départ, un palier plus haut à chaque jalon, sommet.
        $n = count($milestones);
        $xs = [$this->x0];
        $ys = [8.0];
        foreach ($milestones as $i => $m) {
            $xs[] = $m->at;
            $ys[] = 16 + 58 * ($n > 1 ? $i / ($n - 1) : 0);
        }
        $xs[] = $this->x1;
        $ys[] = 94.0;

        $this->base = self::monotone($xs, $ys);
    }

    public static function today(): float
    {
        return (float) date('Y') + (int) date('z') / 365;
    }

    public function render(): string
    {
        $todayX = $this->x($this->today);
        $points = [];
        for ($i = 0; $i <= 320; $i++) {
            $year = $this->x0 + ($this->x1 - $this->x0) * $i / 320;
            $points[] = sprintf('%.1f,%.1f', $this->x($year), $this->y($this->altitude($year)));
        }
        $line = 'M' . implode(' L', $points);
        $area = $line . sprintf(' L%.1f,%d L%.1f,%d Z', $this->x($this->x1), self::AXIS, $this->x($this->x0), self::AXIS);

        $svg = [];
        $svg[] = sprintf('<svg class="profile" viewBox="0 0 %d %d" role="img" aria-labelledby="profile-title" data-profile>', self::W, self::H);
        $svg[] = '<title id="profile-title">' . $this->e($this->summary()) . '</title>';
        $svg[] = '<defs>'
            . '<pattern id="profile-dots" width="7" height="7" patternUnits="userSpaceOnUse"><circle cx="3.5" cy="3.5" r="1.6"/></pattern>'
            . sprintf('<linearGradient id="profile-fade" gradientUnits="userSpaceOnUse" x1="0" y1="%d" x2="0" y2="%d"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#fff" stop-opacity=".22"/></linearGradient>', self::TOP, self::AXIS)
            . sprintf('<mask id="profile-mask"><rect width="%d" height="%d" fill="url(#profile-fade)"/></mask>', self::W, self::H)
            . sprintf('<clipPath id="profile-past"><rect class="profile__wipe" width="%.1f" height="%d"/></clipPath>', $todayX, self::H)
            . sprintf('<clipPath id="profile-future"><rect x="%.1f" width="%.1f" height="%d"/></clipPath>', $todayX, self::W - $todayX, self::H)
            . '</defs>';

        // Axe du temps : une graduation par année
        $svg[] = sprintf('<line class="profile__axis" x1="%d" y1="%d" x2="%d" y2="%d"/>', self::LEFT, self::AXIS, self::W - self::RIGHT + 30, self::AXIS);
        for ($year = (int) ceil($this->x0); $year <= (int) floor($this->x1); $year++) {
            $x = $this->x((float) $year);
            $svg[] = sprintf('<g class="profile__tick"><line x1="%.1f" y1="%d" x2="%.1f" y2="%d"/><text x="%.1f" y="%d">%d</text></g>', $x, self::AXIS, $x, self::AXIS + 7, $x, self::AXIS + 26, $year);
        }

        // Le relief : trame de points sous la courbe, violette jusqu'à aujourd'hui, grise ensuite
        $svg[] = sprintf('<g mask="url(#profile-mask)"><path class="profile__area profile__area--past" d="%s" clip-path="url(#profile-past)"/><path class="profile__area profile__area--future" d="%s" clip-path="url(#profile-future)"/></g>', $area, $area);

        // La route : pleine jusqu'à aujourd'hui, en pointillés ensuite
        $svg[] = sprintf('<path class="profile__road profile__road--future" d="%s" clip-path="url(#profile-future)"/>', $line);
        $svg[] = sprintf('<path class="profile__road profile__road--past" d="%s" clip-path="url(#profile-past)" pathLength="1"/>', $line);

        // Les jalons
        foreach ($this->stops as $i => $m) {
            $x = $this->x($m->at);
            $y = $this->y($this->altitude($m->at));
            $row = self::ROWS[$i % 2];
            $anchor = $x < 150 ? 'start' : ($x > self::W - 220 ? 'end' : 'middle');

            $svg[] = sprintf(
                '<g class="profile__stop is-%s" data-step="%d" tabindex="0" role="button" aria-label="%s" style="--d: %.2f">'
                . '<line class="profile__leader" x1="%.1f" y1="%d" x2="%.1f" y2="%.1f"/>'
                . '<circle class="profile__dot" cx="%.1f" cy="%.1f" r="6.5"/>'
                . '<text class="profile__period" x="%.1f" y="%d" text-anchor="%s">%s</text>'
                . '<text class="profile__name" x="%.1f" y="%d" text-anchor="%s">%s</text>'
                . '</g>',
                $this->e($m->state), $i, $this->e($m->title . ', ' . $m->period), $this->progress($m->at),
                $x, $row + 14, $x, $y - 11,
                $x, $y,
                $x, $row - 18, $anchor, $this->e(mb_strtoupper($m->period)),
                $x, $row + 2, $anchor, $this->e($m->title),
            );
        }

        // Le sommet : le cap
        $sx = $this->x($this->x1);
        $sy = $this->y($this->altitude($this->x1));
        // un grand mât planté au sommet, le cap écrit tout en haut, au-dessus des étiquettes
        $svg[] = sprintf(
            '<g class="profile__summit" style="--d: 1"><line x1="%.1f" y1="%.1f" x2="%.1f" y2="30"/><path d="M%.1f,30 l22,8 l-22,8 z"/>'
            . '<text class="profile__period" x="%.1f" y="24" text-anchor="end">%s</text>'
            . '<text class="profile__name" x="%.1f" y="45" text-anchor="end">%s</text></g>',
            $sx, $sy - 2, $sx, $sx,
            $sx - 10, $this->e(($this->t)('LE CAP')), $sx - 10, $this->e($this->cap),
        );

        // Aujourd'hui : le papillon du titre, posé sur la route, là où le trait plein devient pointillé
        $todayY = $this->y($this->altitude($this->today));
        $wing = '<path class="wing__top" d="M-1 -2 C -6 -18, -21 -19, -20 -9 C -19 -2, -9 1, -1 0 Z"/>'
            . '<path class="wing__bottom" d="M-1 0 C -8 1, -17 5, -14 12 C -11 16, -4 9, -1 2 Z"/>'
            . '<circle class="wing__spot" cx="-13" cy="-9" r="2.2"/>';
        $svg[] = sprintf(
            '<g class="profile__today" style="--d: %.2f" aria-hidden="true"><g transform="translate(%.1f %.1f) rotate(-12)">'
            . '<g class="profile__butterfly">'
            . '<g class="wing">%s</g><g transform="scale(-1 1)"><g class="wing">%s</g></g>'
            . '<g class="butterfly__body"><ellipse cx="0" cy="1" rx="1.6" ry="8"/><path d="M-0.5 -6 C -2 -11, -4 -14, -6 -15 M0.5 -6 C 2 -11, 4 -14, 6 -15"/></g>'
            . '</g></g></g>',
            $this->progress($this->today), $todayX, $todayY - 17, $wing, $wing,
        );

        $svg[] = '</svg>';

        return implode("\n", $svg);
    }

    /**
     * Altitude (0 à 100) à une date : la pente de base, plus un léger relief.
     */
    private function altitude(float $year): float
    {
        // plusieurs fréquences sans rapport simple : un relief irrégulier, pas une sinusoïde
        $relief = 1.9 * sin($year * 7.3) + 1.2 * sin($year * 17.9 + 1.7) + 0.6 * sin($year * 41.3 + 0.4);
        // pas de relief aux extrémités, pour un départ et une arrivée propres
        $fade = min(1, ($year - $this->x0) * 2, ($this->x1 - $year) * 2);

        return ($this->base)($year) + $relief * max(0, $fade);
    }

    private function x(float $year): float
    {
        return self::LEFT + ($year - $this->x0) / ($this->x1 - $this->x0) * (self::W - self::LEFT - self::RIGHT);
    }

    private function y(float $altitude): float
    {
        return self::AXIS - $altitude / 100 * (self::AXIS - self::TOP);
    }

    /** Position de 0 à 1 le long du profil (sert à cadencer les animations). */
    private function progress(float $year): float
    {
        return ($year - $this->x0) / ($this->x1 - $this->x0);
    }

    private function summary(): string
    {
        $parts = array_map(static fn (Milestone $m): string => $m->title . ' (' . $m->period . ')', $this->stops);

        return ($this->t)('Profil du parcours : ') . implode(', ', $parts) . ($this->t)(', puis ') . $this->cap . '.';
    }

    /**
     * Interpolation cubique monotone (Fritsch-Carlson) : la courbe passe par chaque point
     * sans jamais redescendre entre deux paliers croissants.
     *
     * @param list<float> $xs
     * @param list<float> $ys
     * @return callable(float): float
     */
    private static function monotone(array $xs, array $ys): callable
    {
        $n = count($xs);
        $d = [];
        for ($k = 0; $k < $n - 1; $k++) {
            $d[$k] = ($ys[$k + 1] - $ys[$k]) / max(1e-6, $xs[$k + 1] - $xs[$k]);
        }

        $m = [$d[0]];
        for ($k = 1; $k < $n - 1; $k++) {
            $m[$k] = $d[$k - 1] * $d[$k] <= 0 ? 0.0 : ($d[$k - 1] + $d[$k]) / 2;
        }
        $m[$n - 1] = $d[$n - 2];

        for ($k = 0; $k < $n - 1; $k++) {
            if ($d[$k] == 0.0) {
                $m[$k] = $m[$k + 1] = 0.0;
                continue;
            }
            $a = $m[$k] / $d[$k];
            $b = $m[$k + 1] / $d[$k];
            $h = $a * $a + $b * $b;
            if ($h > 9) {
                $t = 3 / sqrt($h);
                $m[$k] = $t * $a * $d[$k];
                $m[$k + 1] = $t * $b * $d[$k];
            }
        }

        return static function (float $x) use ($xs, $ys, $m, $n): float {
            if ($x <= $xs[0]) {
                return $ys[0];
            }
            if ($x >= $xs[$n - 1]) {
                return $ys[$n - 1];
            }
            $k = 0;
            while ($k < $n - 2 && $x > $xs[$k + 1]) {
                $k++;
            }
            $h = $xs[$k + 1] - $xs[$k];
            $t = ($x - $xs[$k]) / $h;
            $t2 = $t * $t;
            $t3 = $t2 * $t;

            return (2 * $t3 - 3 * $t2 + 1) * $ys[$k] + ($t3 - 2 * $t2 + $t) * $h * $m[$k]
                + (-2 * $t3 + 3 * $t2) * $ys[$k + 1] + ($t3 - $t2) * $h * $m[$k + 1];
        };
    }

    private function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

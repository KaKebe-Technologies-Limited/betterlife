<?php
/**
 * Painted brush-stroke generator. Returns SVG path data for a filled stroke
 * from (x1,y1) to (x2,y2): blunt loaded start, slightly wobbling body and a
 * dry, tapering tail that breaks into bristle streaks. Deterministic per
 * seed so the design never shifts between page loads. Pair with the
 * #lpBrush filter for edge grain.
 */
if (!function_exists('lp_brush_d')):
function lp_brush_d(float $x1, float $y1, float $x2, float $y2, float $w, int $seed = 1, float $bend = 0.05): string
{
    mt_srand($seed);
    $dx = $x2 - $x1; $dy = $y2 - $y1; $L = hypot($dx, $dy);
    $nx = -$dy / $L; $ny = $dx / $L;
    $center = fn(float $s): array => [
        $x1 + $dx * $s + $nx * sin(M_PI * $s) * $bend * $L,
        $y1 + $dy * $s + $ny * sin(M_PI * $s) * $bend * $L,
    ];
    $ph1 = mt_rand(0, 628) / 100; $ph2 = mt_rand(0, 628) / 100;
    $fmt = fn(array $p): string => round($p[0], 1) . ',' . round($p[1], 1);

    $N = 40; $top = []; $bot = [];
    for ($i = 0; $i <= $N; $i++) {
        $s = $i / $N;
        if ($s < 0.07)      $p = 0.6 + 0.4 * sin($s / 0.07 * M_PI / 2);          // blunt start
        elseif ($s > 0.7)   $p = 1 - 0.6 * (($s - 0.7) / 0.3) ** 1.3;            // dry taper
        else                $p = 1;
        $wob = 1 + 0.07 * sin($s * 7 + $ph1) + 0.04 * sin($s * 19 + $ph2);
        [$cx, $cy] = $center($s);
        $hT = $w / 2 * $p * $wob * (1 + mt_rand(-35, 35) / 1000);
        $hB = $w / 2 * $p * $wob * (1 + mt_rand(-35, 35) / 1000);
        $top[] = [$cx + $nx * $hT, $cy + $ny * $hT];
        $bot[] = [$cx - $nx * $hB, $cy - $ny * $hB];
    }
    $d = 'M' . implode('L', array_map($fmt, $top)) . 'L' . implode('L', array_map($fmt, array_reverse($bot))) . 'Z';

    // Bristle streaks running off the tail
    $k = 6 + mt_rand(0, 3);
    for ($b = 0; $b < $k; $b++) {
        $off = mt_rand(-90, 90) / 100 * $w * 0.3;
        $s0  = 0.55 + mt_rand(0, 20) / 100;
        $s1  = 1.0 + mt_rand(-6, 14) / 100;
        $bw  = $w * (0.05 + mt_rand(0, 9) / 100);
        $pts = [];
        foreach ([0, 0.5, 1] as $t) {
            $s = $s0 + ($s1 - $s0) * $t;
            [$cx, $cy] = $center($s);
            $h = $bw / 2 * (1 - 0.85 * $t);
            $pts['t'][] = [$cx + $nx * ($off + $h), $cy + $ny * ($off + $h)];
            $pts['b'][] = [$cx + $nx * ($off - $h), $cy + $ny * ($off - $h)];
        }
        $d .= 'M' . implode('L', array_map($fmt, $pts['t'])) . 'L' . implode('L', array_map($fmt, array_reverse($pts['b']))) . 'Z';
    }
    return $d;
}
endif;

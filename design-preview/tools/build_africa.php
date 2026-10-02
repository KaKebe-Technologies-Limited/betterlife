<?php
/**
 * Builds a seam-free Africa silhouette (incl. Madagascar) + a world-map
 * backdrop from Natural Earth 1:50m admin-0 countries (public domain),
 * projected with Web Mercator. Output coordinates are in "design px" for
 * the hero art box (viewBox 0 0 880 720 at a 1440px-wide page).
 */
ini_set('memory_limit', '1G');
// Usage: php build_africa.php <output dir> <path to ne_50m_admin_0_countries.geojson>
// Data: https://raw.githubusercontent.com/nvkelso/natural-earth-vector/v5.1.2/geojson/ne_50m_admin_0_countries.geojson
$dir  = sys_get_temp_dir();
$geo  = json_decode(file_get_contents($argv[2] ?? __DIR__ . '/ne_50m_admin_0_countries.geojson'), true);

$merc = fn(float $lat): float => log(tan(M_PI / 4 + deg2rad($lat) / 2));

/* ---------- 1. Collect rings ---------- */
$africaPolys = [];   // list of polygons, each = list of rings (lon/lat arrays)
$otherPolys  = [];
foreach ($geo['features'] as $f) {
    $p = $f['properties'];
    $g = $f['geometry'];
    $polys = $g['type'] === 'Polygon' ? [$g['coordinates']] : $g['coordinates'];
    if (($p['CONTINENT'] ?? '') === 'Africa') {
        foreach ($polys as $poly) $africaPolys[] = ['name' => $p['ADMIN'], 'rings' => $poly];
    } else {
        foreach ($polys as $poly) $otherPolys[] = $poly;
    }
}
fwrite(STDERR, "African polygons: " . count($africaPolys) . "\n");

/* ---------- 2. Dissolve internal borders: keep edges used exactly once ---------- */
$key = fn(array $pt): string => sprintf('%.7f,%.7f', $pt[0], $pt[1]);
$edgeCount = [];
$edges = [];
foreach ($africaPolys as $poly) {
    foreach ($poly['rings'] as $ring) {
        $n = count($ring);
        for ($i = 0; $i < $n - 1; $i++) {
            $a = $key($ring[$i]); $b = $key($ring[$i + 1]);
            if ($a === $b) continue;
            $u = $a < $b ? "$a|$b" : "$b|$a";
            $edgeCount[$u] = ($edgeCount[$u] ?? 0) + 1;
            $edges[$u] = [$ring[$i], $ring[$i + 1]];
        }
    }
}
$total = count($edgeCount);
$boundary = array_filter($edgeCount, fn($c) => $c === 1);
fwrite(STDERR, "edges: $total, boundary (used once): " . count($boundary) . ", shared: " . ($total - count($boundary)) . "\n");

// adjacency over boundary edges
$adj = [];
foreach ($boundary as $u => $_) {
    [$a, $b] = explode('|', $u);
    $adj[$a][] = $b; $adj[$b][] = $a;
}
$odd = 0; foreach ($adj as $k => $v) if (count($v) !== 2) $odd++;
fwrite(STDERR, "boundary vertices with degree != 2: $odd\n");

$coords = [];
foreach ($edges as $u => [$p1, $p2]) { $coords[$key($p1)] = $p1; $coords[$key($p2)] = $p2; }

// walk rings
$used = [];
$rings = [];
foreach ($boundary as $u => $_) {
    if (isset($used[$u])) continue;
    [$start, $next] = explode('|', $u);
    $used[$u] = true;
    $ring = [$coords[$start]];
    $prev = $start; $cur = $next;
    $guard = 0;
    while ($cur !== $start && $guard++ < 200000) {
        $ring[] = $coords[$cur];
        $found = null;
        foreach ($adj[$cur] as $cand) {
            $e = $cur < $cand ? "$cur|$cand" : "$cand|$cur";
            if (!isset($used[$e])) { $found = $cand; $used[$e] = true; break; }
        }
        if ($found === null) break; // open chain (should not happen)
        $prev = $cur; $cur = $found;
    }
    $closed = $cur === $start;
    $rings[] = ['pts' => $ring, 'closed' => $closed];
}
$open = count(array_filter($rings, fn($r) => !$r['closed']));
fwrite(STDERR, "dissolved rings: " . count($rings) . " (open: $open)\n");

/* ---------- 3. Projection fitted to the reference layout ---------- */
// Area + bbox in lon/lat (Mercator) to drop specks before fitting
$projRaw = fn(array $pt): array => [deg2rad($pt[0]), -$merc($pt[1])];
$area = function (array $pts): float {
    $s = 0; $n = count($pts);
    for ($i = 0; $i < $n; $i++) { $j = ($i + 1) % $n; $s += $pts[$i][0] * $pts[$j][1] - $pts[$j][0] * $pts[$i][1]; }
    return abs($s) / 2;
};
foreach ($rings as &$r) { $r['raw'] = array_map($projRaw, $r['pts']); $r['area'] = $area($r['raw']); }
unset($r);
usort($rings, fn($a, $b) => $b['area'] <=> $a['area']);
$mainArea = $rings[0]['area'];

// Fit: Africa (mainland + Madagascar) top-left at (40,53), height 620 design px
$minX = INF; $maxX = -INF; $minY = INF; $maxY = -INF;
foreach ($rings as $r) {
    if ($r['area'] < $mainArea * 0.002) continue; // ignore islands for the fit
    foreach ($r['raw'] as [$x, $y]) { $minX = min($minX, $x); $maxX = max($maxX, $x); $minY = min($minY, $y); $maxY = max($maxY, $y); }
}
$S  = 620 / ($maxY - $minY);
$ox = 40 - $minX * $S;
$oy = 53 - $minY * $S;
$P  = fn(array $raw): array => [$raw[0] * $S + $ox, $raw[1] * $S + $oy];
fwrite(STDERR, sprintf("scale %.2f px/rad, Africa box %.1f x %.1f (aspect %.3f)\n", $S, ($maxX - $minX) * $S, 620, ($maxX - $minX) / ($maxY - $minY)));

/* ---------- 4. Simplify (Ramer–Douglas–Peucker) ---------- */
function rdp(array $pts, float $eps): array {
    $n = count($pts);
    if ($n < 3) return $pts;
    $dmax = 0; $idx = 0;
    [$x1, $y1] = $pts[0]; [$x2, $y2] = $pts[$n - 1];
    $dx = $x2 - $x1; $dy = $y2 - $y1; $len = hypot($dx, $dy);
    for ($i = 1; $i < $n - 1; $i++) {
        [$x, $y] = $pts[$i];
        $d = $len == 0 ? hypot($x - $x1, $y - $y1) : abs($dy * $x - $dx * $y + $x2 * $y1 - $y2 * $x1) / $len;
        if ($d > $dmax) { $dmax = $d; $idx = $i; }
    }
    if ($dmax > $eps) {
        $a = rdp(array_slice($pts, 0, $idx + 1), $eps);
        $b = rdp(array_slice($pts, $idx), $eps);
        return array_merge(array_slice($a, 0, -1), $b);
    }
    return [$pts[0], $pts[$n - 1]];
}
function ringPath(array $pts, float $eps): string {
    // split closed ring in two halves so RDP keeps both "ends"
    $h = intdiv(count($pts), 2);
    $s = array_merge(rdp(array_slice($pts, 0, $h + 1), $eps), array_slice(rdp(array_slice($pts, $h), $eps), 1));
    $d = '';
    foreach ($s as $i => [$x, $y]) $d .= ($i ? 'L' : 'M') . round($x, 1) . ',' . round($y, 1);
    return $d . 'Z';
}

$africaD = ''; $kept = 0; $dropped = 0; $pts = 0;
foreach ($rings as $r) {
    $proj = array_map($P, $r['raw']);
    $aPx = $r['area'] * $S * $S;
    if ($aPx < 30) { $dropped++; continue; } // specks smaller than ~5x6px
    $africaD .= ringPath($proj, 0.3);
    $kept++;
}
fwrite(STDERR, "Africa rings kept: $kept, tiny islands dropped: $dropped, path bytes: " . strlen($africaD) . "\n");

/* ---------- 5. World backdrop (everything else), clamped to a margin box ---------- */
// Backdrop runs well past the 880-wide art box so it reaches the page edge
// on wide screens (the hero clips it there).
$VB = [0, 0, 1700, 720];
$M = 60; // margin outside the viewBox where clamping artefacts stay invisible
$worldD = '';
foreach ($otherPolys as $poly) {
    foreach ($poly as $ri => $ring) {
        $proj = [];
        $inside = false;
        foreach ($ring as $pt) {
            if ($pt[1] > 85 || $pt[1] < -85) $pt[1] = max(-85, min(85, $pt[1]));
            [$x, $y] = $P($projRaw($pt));
            if ($x > $VB[0] - 5 && $x < $VB[2] + 5 && $y > $VB[1] - 5 && $y < $VB[3] + 5) $inside = true;
            $proj[] = [max($VB[0] - $M, min($VB[2] + $M, $x)), max($VB[1] - $M, min($VB[3] + $M, $y))];
        }
        if (!$inside) continue;
        // drop consecutive duplicates created by clamping
        $clean = [];
        foreach ($proj as $p) { if (!$clean || abs(end($clean)[0] - $p[0]) > 0.01 || abs(end($clean)[1] - $p[1]) > 0.01) $clean[] = $p; }
        if (count($clean) < 3 || $area($clean) < 20) continue;
        $worldD .= ringPath($clean, 0.8); // backdrop only: coarser than Africa
    }
}
fwrite(STDERR, "world path bytes: " . strlen($worldD) . "\n");

$outDir = $argv[1] ?? $dir;
file_put_contents("$outDir/map-paths.php", "<?php\n// GENERATED by tools/build_africa.php from Natural Earth 1:50m admin-0\n// countries (public domain, naturalearthdata.com). Web Mercator, design px\n// for the 880x720 hero art box (Africa box: x 40, y 53, h 620). Do not edit.\nreturn " . var_export(['africa' => $africaD, 'world' => $worldD, 'africa_box' => [40, 53, round(($maxX - $minX) * $S, 1), 620]], true) . ";\n");
file_put_contents("$dir/africa-d.txt", $africaD);
file_put_contents("$dir/world-d.txt", $worldD);
file_put_contents("$dir/map-meta.json", json_encode(['scale_px_per_rad' => $S, 'ox' => $ox, 'oy' => $oy, 'africa_box' => [40, 53, ($maxX - $minX) * $S, 620]], JSON_PRETTY_PRINT));

// Quick check render: Africa solid over world backdrop, plus lon/lat graticule labels
$check = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 880 720" width="880" height="720" style="background:#fff">'
    . '<path d="' . $worldD . '" fill="#e9e2d6"/>'
    . '<path d="' . $africaD . '" fill="#08722d" fill-rule="nonzero"/>';
foreach ([-20, 0, 20, 40, 60, 80] as $lon) { [$x] = $P($projRaw([$lon, 0])); $check .= "<line x1='$x' y1='0' x2='$x' y2='720' stroke='#c00' stroke-width='.5' opacity='.5'/><text x='" . ($x + 2) . "' y='712' font-size='10' fill='#c00'>{$lon}°</text>"; }
foreach ([-30, -15, 0, 15, 30] as $lat) { [, $y] = $P($projRaw([0, $lat])); $check .= "<line x1='0' y1='$y' x2='880' y2='$y' stroke='#c00' stroke-width='.5' opacity='.5'/><text x='2' y='" . ($y - 2) . "' font-size='10' fill='#c00'>{$lat}°</text>"; }
file_put_contents("$dir/africa-check.svg", $check . '</svg>');

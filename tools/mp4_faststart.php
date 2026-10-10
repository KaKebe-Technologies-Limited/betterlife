<?php
// Moves an MP4's index ("moov") in front of its media data ("mdat"), so playback can start while the
// rest downloads. The chunk offsets inside the index (stco/co64) are shifted by the index's size.
// Usage: C:\xampp\php\php.exe -d memory_limit=768M tools/mp4_faststart.php in.mp4 out.mp4
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
[$_, $in, $out] = $argv;
$data = file_get_contents($in);
$size = strlen($data);

// Top-level boxes
$boxes = [];
for ($pos = 0; $pos < $size;) {
    $len = unpack('N', substr($data, $pos, 4))[1];
    $type = substr($data, $pos + 4, 4);
    if ($len === 1) $len = unpack('J', substr($data, $pos + 8, 8))[1];
    elseif ($len === 0) $len = $size - $pos;
    $boxes[] = ['type' => $type, 'pos' => $pos, 'len' => $len];
    $pos += $len;
}
$types = array_column($boxes, 'type');
$moovI = array_search('moov', $types, true);
$mdatI = array_search('mdat', $types, true);
if ($moovI === false || $mdatI === false) exit("no moov or mdat\n");
if ($moovI < $mdatI) { copy($in, $out); exit("already fast start\n"); }

$moov = substr($data, $boxes[$moovI]['pos'], $boxes[$moovI]['len']);
$shift = strlen($moov);   // everything before moov's old place moves down by its size

// Patch chunk offsets inside moov
$containers = ['moov', 'trak', 'mdia', 'minf', 'stbl', 'edts', 'dinf', 'mvex', 'udta'];
$patched = 0;
$walk = function (int $start, int $end) use (&$walk, &$moov, $containers, $shift, &$patched) {
    for ($p = $start; $p + 8 <= $end;) {
        $len = unpack('N', substr($moov, $p, 4))[1];
        $type = substr($moov, $p + 4, 4);
        $head = 8;
        if ($len === 1) { $len = unpack('J', substr($moov, $p + 8, 8))[1]; $head = 16; }
        if ($len < 8) break;
        if (in_array($type, $containers, true)) {
            $walk($p + $head, $p + $len);
        } elseif ($type === 'stco' || $type === 'co64') {
            $n = unpack('N', substr($moov, $p + $head + 4, 4))[1];
            $q = $p + $head + 8;
            for ($i = 0; $i < $n; $i++) {
                if ($type === 'stco') {
                    $v = unpack('N', substr($moov, $q, 4))[1] + $shift;
                    if ($v > 0xFFFFFFFF) exit("offset too large for stco\n");
                    $moov = substr_replace($moov, pack('N', $v), $q, 4); $q += 4;
                } else {
                    $v = unpack('J', substr($moov, $q, 8))[1] + $shift;
                    $moov = substr_replace($moov, pack('J', $v), $q, 8); $q += 8;
                }
                $patched++;
            }
        }
        $p += $len;
    }
};
$walk(8, strlen($moov));

// New order: everything before mdat (ftyp etc.), then moov, then the rest without the old moov
$outData = '';
foreach ($boxes as $i => $b) {
    if ($i === $mdatI) $outData .= $moov;
    if ($i === $moovI) continue;
    $outData .= substr($data, $b['pos'], $b['len']);
}
file_put_contents($out, $outData);
echo "moved index to the front: $patched offsets shifted by $shift bytes; " . number_format(strlen($outData)) . " bytes\n";

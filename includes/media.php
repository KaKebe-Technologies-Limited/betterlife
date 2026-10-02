<?php
/**
 * Shared photo helpers for the About, Programmes and Project pages:
 * responsive images, lightbox photos, the painted brush filter and the
 * photo viewer dialog. Resized copies come from tools/make_sized_images.php.
 */
require_once __DIR__ . '/brush.php';

if (!function_exists('ab_variants')) {
    /** Resized copies made by tools/make_sized_images.php live in assets/img/sized/. */
    function ab_variants(string $path): array
    {
        static $cache = [];
        if (isset($cache[$path])) return $cache[$path];
        $slug = preg_replace('~\.[a-z0-9]+$~i', '', str_replace('/', '__', preg_replace('~^assets/img/~', '', $path)));
        $out = [];
        foreach ([480, 960, 1600] as $w) {
            $rel = "assets/img/sized/{$slug}-{$w}.webp";
            if (is_file(dirname(__DIR__) . '/' . $rel)) $out[$w] = $rel;
        }
        return $cache[$path] = $out;
    }

    /** <img> with intrinsic size (no layout shift), responsive srcset and lazy loading by default. */
    function ab_img(string $path, string $alt, string $class = '', bool $lazy = true, string $extra = '', string $sizes = '100vw'): string
    {
        static $dims = [];
        $dims[$path] ??= @getimagesize(dirname(__DIR__) . '/' . $path) ?: [null, null];
        [$w, $h] = $dims[$path];
        $v = ab_variants($path);
        $src = $v[960] ?? $path;
        // Offer the original too when it is larger than the biggest resized copy
        if ($v && $w && $w > max(array_keys($v))) $v[$w] = $path;
        $srcset = $v ? implode(', ', array_map(fn($vw, $rel) => asset_url($rel) . " {$vw}w", array_keys($v), $v)) : '';
        return '<img src="' . h(asset_url($src)) . '"' . ($srcset ? ' srcset="' . h($srcset) . '" sizes="' . h($sizes) . '"' : '')
            . ' alt="' . h($alt) . '"'
            . ($w ? ' width="' . $w . '" height="' . $h . '"' : '')
            . ($class ? ' class="' . h($class) . '"' : '')
            . ($lazy ? ' loading="lazy" decoding="async"' : ' fetchpriority="high"')
            . ($extra ? ' ' . $extra : '') . '>';
    }

    /** Split text into its first sentence and the remainder. */
    function ab_split(string $text): array
    {
        $text = trim($text);
        if (preg_match('/^(.+?[.!?])\s+(.+)$/su', $text, $m)) return [$m[1], $m[2]];
        return [$text, ''];
    }

    /** Photo that opens in the lightbox (a plain link to the full image without JavaScript). */
    function ab_photo(string $path, string $alt, string $caption, string $gallery, string $class = '', string $sizes = '400px', string $extra = ''): string
    {
        return '<a href="' . h(asset_url($path)) . '" class="ab-lb ' . h($class) . '" data-gallery="' . h($gallery) . '" data-caption="' . h($caption) . '" aria-label="View larger: ' . h($caption) . '"' . ($extra ? ' ' . $extra : '') . '>'
            . ab_img($path, $alt, '', true, '', $sizes) . '<span class="ab-lb-icon" aria-hidden="true">' . icon('search', 16) . '</span></a>';
    }

    /** Hidden SVG filter that turns plain paths into painted brush strokes (same as the homepage). */
    function ab_brush_defs(): string
    {
        return '<svg class="ab-defs" width="0" height="0" aria-hidden="true" focusable="false"><defs>'
            . '<filter id="lpBrush" x="-15%" y="-60%" width="130%" height="220%" color-interpolation-filters="sRGB">'
            . '<feTurbulence type="fractalNoise" baseFrequency="0.035" numOctaves="3" seed="4" result="edgeNoise"/>'
            . '<feDisplacementMap in="SourceGraphic" in2="edgeNoise" scale="6" xChannelSelector="R" yChannelSelector="G" result="rough"/>'
            . '<feTurbulence type="fractalNoise" baseFrequency="0.006 0.42" numOctaves="2" seed="9" result="bristles"/>'
            . '<feColorMatrix in="bristles" type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  4.2 0 0 0 -1.55" result="streaks"/>'
            . '<feComposite in="rough" in2="streaks" operator="in" result="dry"/>'
            . '<feMorphology in="rough" operator="erode" radius="3" result="core"/>'
            . '<feMerge><feMergeNode in="dry"/><feMergeNode in="core"/></feMerge>'
            . '</filter></defs></svg>';
    }

    /** Painted highlighter under a word in a headline. */
    function ab_mark(string $text, int $seed = 47): string
    {
        return '<span class="ab-mark">' . h($text) . '<svg viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true"><path filter="url(#lpBrush)" d="'
            . lp_brush_d(4, 17, 238, 14, 22, $seed, 0.02) . '"/></svg></span>';
    }

    /** Photo viewer (native dialog: focus is contained and Escape closes it). */
    function ab_lightbox(): string
    {
        return '<dialog class="ab-lightbox" id="abLightbox" aria-label="Photo viewer"><figure><img src="" alt="">'
            . '<figcaption><span class="ab-lb-caption"></span><span class="ab-lb-count"></span></figcaption></figure>'
            . '<button type="button" class="ab-lb-btn ab-lb-close" aria-label="Close photo viewer">' . icon('x', 22) . '</button>'
            . '<button type="button" class="ab-lb-btn ab-lb-prev" aria-label="Previous photo">' . icon('arrow-right', 20) . '</button>'
            . '<button type="button" class="ab-lb-btn ab-lb-next" aria-label="Next photo">' . icon('arrow-right', 20) . '</button></dialog>';
    }
}

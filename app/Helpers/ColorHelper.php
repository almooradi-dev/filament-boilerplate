<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Convert a hex color string to an RGB array.
     *
     * @param  string  $hex  Hex color (e.g. "#ff0000" or "ff0000")
     * @return array{0: int, 1: int, 2: int}  [R, G, B]
     */
    protected function hex2rgb($hex)
    {
        $hex = ltrim($hex, '#');
        [$r, $g, $b] = sscanf($hex, "%02x%02x%02x");

        return [$r, $g, $b];
    }
}

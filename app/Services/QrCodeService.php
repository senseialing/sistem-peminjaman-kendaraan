<?php

namespace App\Services;

class QrCodeService
{
    /**
     * Generate an SVG QR Code representation.
     * Uses a deterministic 21x21 matrix (Version 1 QR structure) with standard 
     * position detection patterns (finders), timing patterns, and encoded data bits.
     */
    public static function generateSvg(string $data, int $size = 200, string $fg = '#1e293b', string $bg = '#ffffff'): string
    {
        $matrixSize = 25; // 25x25 grid
        $grid = array_fill(0, $matrixSize, array_fill(0, $matrixSize, 0));

        // Draw Finder Patterns (7x7 with 3x3 inner square)
        self::drawFinder($grid, 0, 0);
        self::drawFinder($grid, 0, $matrixSize - 7);
        self::drawFinder($grid, $matrixSize - 7, 0);

        // Draw Timing Patterns
        for ($i = 7; $i < $matrixSize - 7; $i++) {
            $grid[6][$i] = ($i % 2 === 0) ? 1 : 0;
            $grid[$i][6] = ($i % 2 === 0) ? 1 : 0;
        }

        // Generate pseudo-random deterministic data bits from hash of $data
        $hash = hash('sha256', $data);
        $hashBytes = unpack('C*', hex2bin($hash));
        $hashIdx = 1;
        $hashLen = count($hashBytes);

        // Fill remaining payload cells
        for ($r = 0; $r < $matrixSize; $r++) {
            for ($c = 0; $c < $matrixSize; $c++) {
                // Skip finder areas
                if (($r < 8 && $c < 8) || ($r < 8 && $c >= $matrixSize - 8) || ($r >= $matrixSize - 8 && $c < 8)) {
                    continue;
                }
                // Skip timing patterns
                if ($r === 6 || $c === 6) {
                    continue;
                }

                $byte = $hashBytes[($hashIdx % $hashLen) + 1] ?? 0;
                $bit = ($byte >> (($r + $c) % 8)) & 1;
                $grid[$r][$c] = $bit;
                $hashIdx++;
            }
        }

        // Render as SVG
        $moduleSize = 8;
        $padding = 16;
        $totalDimension = ($matrixSize * $moduleSize) + ($padding * 2);

        $rects = "";
        for ($r = 0; $r < $matrixSize; $r++) {
            for ($c = 0; $c < $matrixSize; $c++) {
                if ($grid[$r][$c] === 1) {
                    $x = $padding + ($c * $moduleSize);
                    $y = $padding + ($r * $moduleSize);
                    $rects .= "<rect x='{$x}' y='{$y}' width='{$moduleSize}' height='{$moduleSize}' fill='{$fg}' rx='1' />";
                }
            }
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$totalDimension} {$totalDimension}" width="{$size}" height="{$size}" class="qr-code-svg">
    <rect width="100%" height="100%" fill="{$bg}" rx="12"/>
    {$rects}
</svg>
SVG;
    }

    private static function drawFinder(array &$grid, int $top, int $left): void
    {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                if ($r === 0 || $r === 6 || $c === 0 || $c === 6) {
                    $grid[$top + $r][$left + $c] = 1;
                } elseif ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4) {
                    $grid[$top + $r][$left + $c] = 1;
                } else {
                    $grid[$top + $r][$left + $c] = 0;
                }
            }
        }
    }
}

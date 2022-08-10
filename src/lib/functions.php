<?php
namespace CatPaw\CUI;

function size(string $text):array {
    $lines     = \explode("\n", $text);
    $maxHeight = count($lines);
    $maxWidth  = 0;
    $body      = [];

    for ($i = 0; $i < $maxHeight; $i++) {
        $width = \strlen($lines[$i]);
        if ($width > $maxWidth) {
            $maxWidth = $width;
        }
        $body[$i] = LINE_Y.$lines[$i].LINE_Y;
    }

    return [
        'width'  => $maxWidth,
        'height' => $maxHeight,
    ];
}

function pad(string $text, string $filler = ' ', string $edge = ''):array {
    $lines = \explode("\n", $text);
    [
        'width'  => $width,
        'height' => $height,
    ] = size($text);

    for ($i = 0; $i < $height; $i++) {
        $lines[$i] = $edge.\str_pad($lines[$i], $width, $filler).$edge;
    }
    
    return [
        'width'  => $width,
        'height' => $height,
        'lines'  => $lines,
    ];
}
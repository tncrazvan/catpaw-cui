<?php
namespace CatPaw\CUI\Utilities;

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

function timed(Interval $interval, callable $callback) {
    if ($interval->isDue()) {
        $callback();
    }
}


class Dimensions {
    public static int $width  = 0;
    public static int $height = 0;
}

function width(?Interval $interval = null) {
    if (!$interval) {
        $interval = Interval::of(100, 'tw');
    }
    
    if ($interval->isDue()) {
        Dimensions::$width = (int)(`tput cols`);
    }

    return Dimensions::$width;
}

function height(?Interval $interval = null) {
    if (!$interval) {
        $interval = Interval::of(100, 'th');
    }

    if ($interval->isDue()) {
        Dimensions::$height = (int)(`tput lines`);
    }

    return Dimensions::$height;
}
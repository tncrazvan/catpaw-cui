<?php
namespace CatPaw\CUI\Utilities;

use CatPaw\CUI\Shapes;

/**
 * Measure the width and height of a block of text (meaning 1 line or multiple lines of text).
 * @param  string                      $text
 * @return array{width:int,height:int}
 */
function measure(string $text):array {
    $lines     = \explode("\n", $text);
    $maxHeight = count($lines);
    $maxWidth  = 0;
    $body      = [];

    for ($i = 0; $i < $maxHeight; $i++) {
        $width = \strlen($lines[$i]);
        if ($width > $maxWidth) {
            $maxWidth = $width;
        }
        $body[$i] = Shapes::LINE_Y.$lines[$i].Shapes::LINE_Y;
    }

    return [
        'width'  => $maxWidth,
        'height' => $maxHeight,
    ];
}

/**
 * Wrap a block of text (meaning 1 line or multiple lines of text) with some edge strings.
 * @param  string $text
 * @param  string $ml   middle left edge
 * @param  string $mr   middle right edge
 * @param  string $t    top edge
 * @param  string $b    bottom edge
 * @param  string $tl   top left edge
 * @param  string $tr   top right edge
 * @param  string $bl   bottom left edge
 * @param  string $br   bottom right edge
 * @return string the resulting container.
 */
function container(
    string $text,
    string $ml = ' ',
    string $mr = ' ',
    string $t = ' ',
    string $b = ' ',
    string $tl = ' ',
    string $tr = ' ',
    string $bl = ' ',
    string $br = ' ',
):string {
    $text  = \PHP_EOL.$text.PHP_EOL;
    $lines = \explode(\PHP_EOL, $text);
    [
        'width'  => $width,
        'height' => $height,
    ] = measure($text);

    for ($i = 0; $i < $height; $i++) {
        if (0 === $i) {
            $lines[$i] = $tl.$t.\str_repeat($t, $width).$t.$tr;
        } else if ($height - 1 === $i) {
            $lines[$i] = $bl.$b.\str_repeat($b, $width).$b.$br;
        } else {
            $lines[$i] = $ml.' '.\str_pad($lines[$i], $width, ' ').' '.$mr;
        }
    }
    
    return \join(\PHP_EOL, $lines);
}


final class Dimensions {
    public static int $width  = 0;
    public static int $height = 0;
}

function width(?Interval $interval = null) {
    if (!$interval) {
        $interval = Interval::of(100, 'terminal:width');
    }
    
    if ($interval->isDue()) {
        Dimensions::$width = (int)(`tput cols`);
    }

    return Dimensions::$width;
}

function height(?Interval $interval = null) {
    if (!$interval) {
        $interval = Interval::of(100, 'terminal:height');
    }

    if ($interval->isDue()) {
        Dimensions::$height = (int)(`tput lines`);
    }

    return Dimensions::$height;
}
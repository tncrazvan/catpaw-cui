<?php
namespace CatPaw\CUI\Components;

function box(string $text):string {
    $lines     = \explode("\n", $text);
    $maxHeight = count($lines);
    $maxWidth  = 0;
    $body      = [];

    for ($i = 0; $i < $maxHeight; $i++) {
        $width = \strlen($lines[$i]);
        if ($width > $maxWidth) {
            $maxWidth = $width;
        }
        $body[$i] = LINE_Y.$lines[$i];
    }
    
    $xline = str_repeat(LINE_X, \strlen($maxWidth));

    return 
        CORNER_TOP_LEFT.$xline.CORNER_TOP_RIGHT.PHP_EOL
        .join("\n", $body).PHP_EOL
        .CORNER_BOTTOM_LEFT.$xline.CORNER_BOTTOM_RIGHT.PHP_EOL;
}
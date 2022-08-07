<?php
namespace CatPaw\CUI\Components;

use function CatPaw\CUI\pad;
use function CatPaw\CUI\size;

function box(string $text):string {
    [ 'width' => $width ] = size($text);

    [ 'lines' => $$lines ] = pad($text, ' ', \LINE_Y);
    
    $xline = str_repeat(LINE_X, $width);

    return 
        CORNER_TOP_LEFT.$xline.CORNER_TOP_RIGHT.PHP_EOL
        .join("\n", $lines).PHP_EOL
        .CORNER_BOTTOM_LEFT.$xline.CORNER_BOTTOM_RIGHT.PHP_EOL;
}
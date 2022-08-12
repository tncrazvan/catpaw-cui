<?php
namespace CatPaw\CUI\Components;

use CatPaw\CUI\Shapes;

use function CatPaw\CUI\Utilities\container;

function box(string $text):string {
    return container(
        text: $text,
        tl: Shapes::CORNER_TOP_LEFT,
        tr: Shapes::CORNER_TOP_RIGHT,
        ml: Shapes::LINE_Y,
        mr: Shapes::LINE_Y,
        bl: Shapes::CORNER_BOTTOM_LEFT,
        br: Shapes::CORNER_BOTTOM_RIGHT,
        t: Shapes::LINE_X,
        b: Shapes::LINE_X,
    ).\PHP_EOL;
}
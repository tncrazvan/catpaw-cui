<?php
use function CatPaw\CUI\clear;
use function CatPaw\CUI\Colors\foreground;
use function CatPaw\CUI\Components\box;
use function CatPaw\CUI\render;
use function CatPaw\CUI\send;
use function CatPaw\CUI\Utilities\height;
use function CatPaw\CUI\Utilities\width;


function main() {
    render(function() {
        $width  = width();
        $height = height();
        $time   = (new DateTime())->format("H:i:s");

        send(clear());
        send(foreground(255, 0, 0));
        send(box("width: $width, height: $height"));
        send(box("The time is: $time"));
    });
}
<?php
namespace {


    use Amp\Loop;
    use function CatPaw\CUI\clear;
    use function CatPaw\CUI\Colors\foreground;
    
    use function CatPaw\CUI\Components\box;
    use function CatPaw\CUI\send;
    
    use function CatPaw\CUI\Utilities\height;
    use function CatPaw\CUI\Utilities\width;


    function main() {
        Loop::repeat(100, function() {
            $width  = width();
            $height = height();
            yield clear();
            yield send(foreground(255, 0, 0));
            yield send(box("width: $width, height: $height"));
        });
    }
}
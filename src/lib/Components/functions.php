<?php
namespace CatPaw\CUI\Components;

use CatPaw\CUI\Shapes;
use function CatPaw\CUI\Utilities\container as rectangle;

use InvalidArgumentException;

/**
 * Create a box containing some text.
 * @param string|callable(int $pwidth, int $pheight):string $data
 * @return callable(int $pwidth, int $pheight):string
 */
function box(string|callable $data):callable {
    return function(int $pwidth, int $pheight) use ($data) {
        $runnable = rectangle(
            data: $data,
            tl: Shapes::TL,
            tr: Shapes::TR,
            ml: Shapes::Y,
            mr: Shapes::Y,
            bl: Shapes::BL,
            br: Shapes::BR,
            t: Shapes::X,
            b: Shapes::X,
        );
    
        return ($runnable)($pwidth, $pheight).PHP_EOL;
    };
}

/**
 * Create a progress bar.
 * @param  int                      $value
 * @throws InvalidArgumentException
 * @return callable(int $width, int $height):string
 */
function progress(int $value):callable {
    return function(int $pwidth, int $pheight) use ($value) {
        if ($value > 100 || $value < 0) {
            throw new InvalidArgumentException("A progress value must be an integer between 0 and 100, extremes inluded, $value passed.");
        }
    
        $progress = $value / 100;
            
        $progerssWidth = \floor($pwidth * $progress);
    
        $result = [];
        for ($i = 0; $i < $pwidth; $i++) {
            if ($i < $progerssWidth) {
                $result[] = Shapes::X;
            } else {
                $result[] = '-';
            }
        }
    
        $content = \join($result);
        return $content;
    };
}


/**
 * Resize a component.
 * @param int $width
 * @param int $height
 * @param callable(int $pwidth, int $pheight):string $data 
 * @return callable(int $pwidth, int $pheight):string
 */
function resize(int $width, int $height, callable $data):callable {
    return fn(int $w, int $h):string => $data($width > 0?$width:$w, $height > 0?$height:$h);
}
<?php
namespace CatPaw\CUI;

use CatPaw\Attributes\Service;
use function CatPaw\CUI\Colors\foreground;
use function CatPaw\CUI\Components\box;

use function CatPaw\CUI\Components\progress;
use function CatPaw\CUI\Components\resize;

use function CatPaw\CUI\Utilities\active;
use function CatPaw\CUI\Utilities\clear;
use function CatPaw\CUI\Utilities\height;
use function CatPaw\CUI\Utilities\render;
use function CatPaw\CUI\Utilities\send;
use function CatPaw\CUI\Utilities\width;

#[Service]
class App {
    public function __construct() {
        $percentage         = 0;
        $intervalPercentage = Interval::of(100, 'percentage');
        $intervalKeys       = Interval::of(1000, 'keys');

        $key1 = active('1');
        $key2 = active('2');
        $key3 = active('3');

        render(function() use (
            $intervalPercentage,
            $intervalKeys,
            &$percentage,
            &$key1,
            &$key2,
            &$key3,
        ) {
            send(clear());
            match (true) {
                $intervalPercentage->isDue(true) => $percentage++ && $percentage %= 100,
                default                          => false,
            };

            $width  = width();
            $height = height();

            $key1 = $key1 || active('1');
            $key2 = $key2 || active('2');
            $key3 = $key3 || active('3');

            send(foreground(255, 0, 0));
            send(resize(20, 4, box("percentage: $percentage %")));
            send(resize(40, 4, box("width: $width, height: $height")));
            send(resize(20, 4, box(progress($percentage))));

            if ($key1) {
                send(resize(30, 4, box("You pressed key 1.")));
            }
            if ($key2) {
                send(resize(30, 4, box("You pressed key 2.")));
            }
            if ($key3) {
                send(resize(30, 4, box("You pressed key 3.")));
            }

            if ($intervalKeys->isDue(true)) {
                $key1 = false;
                $key2 = false;
                $key3 = false;
            }
        });
    }
}
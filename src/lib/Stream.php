<?php
namespace CatPaw\CUI;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Promise;

class Stream {
    public static ?ResourceOutputStream $out = null;

    public static function initialize() {
        if (self::$out) {
            return;
        }
        self::$out = new ResourceOutputStream(\STDOUT);
    }
    
    public static function send(string $instruction):Promise {
        return self::$out->write($instruction);
    }
}

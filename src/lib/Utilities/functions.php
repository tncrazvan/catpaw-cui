<?php
namespace CatPaw\CUI\Utilities;

use Amp\ByteStream\ClosedException;

use function CatPaw\CUI\Colors\nocolor;
use CatPaw\CUI\Engine;
use CatPaw\CUI\Interval;
use CatPaw\CUI\Shapes;
use Error;

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
        $width = \mb_strlen(trim($lines[$i]));
        if ($width > $maxWidth) {
            $maxWidth = $width;
        }
        $body[$i] = Shapes::Y.$lines[$i].Shapes::X;
    }

    return [
        'w' => $maxWidth,
        'h' => $maxHeight,
    ];
}

function fitWidth(string $text, int $width):string {
    $lines                   = \explode(\PHP_EOL, $text);
    [ 'h' => $actualHeight ] = measure($text);
    $updated                 = [];
    $shift                   = 0;
    for ($i = 0; $i < $actualHeight; $i++) {
        $rawLine = trim($lines[$i]);
        $line    = $lines[$i];
        $delta   = \mb_strlen($line) - \mb_strlen($rawLine);
        $chunks  = \mb_str_split($line, $width + $delta);
        if (!isset($chunks[0])) {
            $chunks = [''];
        }
        for ($j = 0, $chunkLength = count($chunks); $j < $chunkLength; $j++) {
            if ($j > 0) {
                $shift++;
            }
            $updated[$i + $shift] = $chunks[$j];
        }
    }

    return \join(\PHP_EOL, $updated);
}

function fitHeight(string $text, int $height):string {
    $lines   = \explode(\PHP_EOL, $text);
    $updated = \array_slice($lines, 0, $height);
    return \join(\PHP_EOL, $updated);
}


/**
 * Wrap a block of text (meaning 1 line or multiple lines of text) with some edge strings.
 * @param string|callable(int $pwidth, int $pheight):string $data
 * @param string $ml middle left edge
 * @param string $mr middle right edge
 * @param string $t  top edge
 * @param string $b  bottom edge
 * @param string $tl top left edge
 * @param string $tr top right edge
 * @param string $bl bottom left edge
 * @param string $br bottom right edge
 * @param callable(int $pwidth, string $line):string $onLine
 * @return callable(int $pwidth, int $pheight):string
 */
function container(
    string|callable $data,
    string $ml = ' ',
    string $mr = ' ',
    string $t = ' ',
    string $b = ' ',
    string $tl = ' ',
    string $tr = ' ',
    string $bl = ' ',
    string $br = ' ',
):callable {
    return function(int $pwidth, int $pheight) use (
        $data,
        $ml,
        $mr,
        $t,
        $b,
        $tl,
        $tr,
        $bl,
        $br,
    ):string {
        if ($pwidth <= 0 || $pheight <= 0) {
            return '';
        }
        
        if (\is_callable($data)) {
            $data = $data($pwidth - 4, $pheight - 1);
        }


        $data  = \PHP_EOL.$data.\PHP_EOL;
        $lines = \explode(\PHP_EOL, $data);

        if ($pwidth > 0) {
            $data  = fitWidth(\join(\PHP_EOL, $lines), $pwidth - 4);
            $lines = explode(\PHP_EOL, $data);
        }

        if ($pheight > 0) {
            $data  = fitHeight(\join(\PHP_EOL, $lines), $pheight - 1);
            $lines = explode(\PHP_EOL, $data);
        }

        for ($i = 0; $i < $pheight - 1; $i++) {
            if (0 === $i) {
                $lines[$i] = $tl.$t.\str_repeat($t, $pwidth - 4 ).$t.$tr;
            } else if ($pheight - 2 === $i) {
                $lines[$i] = $bl.$b.\str_repeat($b, $pwidth - 4 ).$b.$br;
            } else {
                $line      = mbpad($lines[$i] ?? '', $pwidth - 4, ' ');
                $lines[$i] = $ml.' '.$line.' '.$mr;
            }
        }
        
        return \join(\PHP_EOL, $lines);
    };
}

/**
 * Pad a multibyte string.
 * @param  string $mbstring
 * @param  int    $length
 * @param  string $padding
 * @return string
 */
function mbpad(string $mbstring, int $length, string $padding = ' '):string {
    return join([$mbstring,\str_repeat($padding, $length - \mb_strlen($mbstring))]);
}


final class Dimensions {
    public static int $width  = 0;
    public static int $height = 0;
}

function width(?Interval $interval = null) {
    if (!$interval) {
        $interval = Interval::of(100, 'terminal:width');
    }
    
    if ($interval->isDue(true)) {
        Dimensions::$width = (int)(`tput cols`);
    }

    return Dimensions::$width;
}

function height(?Interval $interval = null) {
    if (!$interval) {
        $interval = Interval::of(100, 'terminal:height');
    }

    if ($interval->isDue(true)) {
        Dimensions::$height = (int)(`tput lines`);
    }

    return Dimensions::$height;
}

/**
 * Send content to the engine's output stream.
 * @param  callable(int $pwidth, int $pheight):string           $data
 * @throws ClosedException
 * @return void
 */
function send(callable $data) {
    return Engine::send($data);
}

/** 
 * Returns a combination of string that will clear the terminal.
 * @return callable(int $pwidth, int $pheight):string
 */
function clear():callable {
    return fn(int $pwidth, int $pheight):string => \join([(nocolor())($pwidth, $pheight),"\033c"]);
}

interface RenderConfiguration {
    public function setInterval(Interval $interval):void;
}

/**
 * Start the engine IO streams and the main loop.
 * @param  callable            $callback
 * @throws Error
 * @return RenderConfiguration
 */
function render(callable $callback):RenderConfiguration {
    Engine::start();
    Engine::setRender($callback);

    return new class implements RenderConfiguration {
        public function setInterval(Interval $interval):void {
            Engine::setOutputInterval($interval);
        }
    };
}

/**
 * Check if a key is active (pressed).
 * @param  string $key
 * @return bool   true if the key is active (meaning it's being pressed), false otherwise.
 */
function active(string $key):bool {
    $active = Engine::$backlog[$key] ?? false;
    if ($active) {
        Engine::$backlog[$key] = false;
    }
    return $active;
}
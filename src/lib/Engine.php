<?php
namespace CatPaw\CUI;

use Amp\ByteStream\ClosedException;

use Amp\ByteStream\ResourceInputStream;
use Amp\ByteStream\ResourceOutputStream;
use function Amp\call;
use Amp\Loop;
use function CatPaw\CUI\Utilities\height;

use function CatPaw\CUI\Utilities\width;

use function CatPaw\milliseconds;

use Closure;

class Engine {
    public static ?ResourceOutputStream $output = null;
    public static ?ResourceInputStream $input   = null;
    public static array $backlog                = [];
    private static array $frame                 = [];
    private static int $outputInterval          = 10;
    private static ?Closure $render             = null;
    private static string $outputLoopID         = '';

    public static function start() {
        self::setInput();
        self::setOutput();
    }

    public static function setRender(callable $render) {
        self::$render = $render;
    }

    /**
     * Try initialize the input stream.
     * Does nothing if the stream has already been initialized.
     * @param  resource $output the input resource (defaults to STDIN).
     * @return void
     */
    public static function setInput($resource = null) {
        if (self::$input) {
            return;
        }
        if (!$resource) {
            $resource = \STDIN;
        }
        self::$input = new ResourceInputStream($resource);
        system("stty -icanon");
        call(function() {
            while (true) {
                $key                 = yield self::$input->read();
                self::$backlog[$key] = milliseconds();
            }
        });
    }

    /**
     * Try initialize the output stream.
     * Does nothing if the stream has already been initialized.
     * @param  resource $output the input resource (defaults to STDOUT).
     * @return void
     */
    public static function setOutput($resource = null) {
        if (self::$output) {
            return;
        }
        if (!$resource) {
            $resource = \STDOUT;
        }
        self::$output = new ResourceOutputStream($resource);

        self::setOutputInterval(Interval::of(self::$outputInterval));
    }

    /**
     * Set the frequency with which the engine should write to $output.
     * @param  int  $milliseconds
     * @return void
     */
    public static function setOutputInterval(Interval $interval):void {
        $milliseconds = $interval->getValue();
        if ($milliseconds <= 0) {
            return;
        }

        self::$outputInterval = $milliseconds;

        if (self::$outputLoopID) {
            Loop::cancel(self::$outputLoopID);
        }

        self::$outputLoopID = Loop::repeat(self::$outputInterval, function() {
            $frame          = self::$frame;
            self::$frame    = [];
            if (!self::$render) {
                return;
            }
            yield call(self::$render);
            yield self::$output->write(join($frame));
        });
    }


    /**
     * Send a string to the output stream.
     * @param  callable(int $pwidth, int $pheight):string           $data
     * @throws ClosedException
     * @return void
     */
    public static function send(callable $data):void {
        $data          = $data(width(), height());
        self::$frame[] = $data;
    }
}

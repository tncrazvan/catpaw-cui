<?php
namespace CatPaw\CUI\Services;

use Amp\ByteStream\ResourceOutputStream;

use function Amp\call;
use function Amp\delay;

use Amp\Process\Process;
use Amp\Process\ProcessInputStream;
use Amp\Process\ProcessOutputStream;
use Amp\Promise;
use CatPaw\Attributes\Entry;
use CatPaw\Attributes\Service;

use Generator;

#[Service]
class ExecutorService {
    private static string $shell                 = 'bash';
    private static int $pid                      = -1;
    private static ?ProcessOutputStream $pstdin  = null;
    private static ?ProcessInputStream $pstdout  = null;
    private static ?ResourceOutputStream $stdout = null;

    public static function setShell(string $shell):void {
        self::$shell = $shell;
    }

    public static function getShell():string {
        return self::$shell;
    }

    private ?Process $process = null;
    #[Entry] public function setup():Generator {
        if (!$this->process) {
            $this->process = new Process(self::$shell);
            self::$pid     = yield $this->process->start();
            self::$pstdin  = $this->process->getStdin();
            self::$pstdout = $this->process->getStdout();
            self::$stdout  = new ResourceOutputStream(\STDOUT);
            call(function() {
                while (true) {
                    $chunk = yield self::$pstdout->read();
                    if ($chunk) {
                        yield delay(100);
                        continue;
                    }
                    yield self::$stdout->write($chunk);
                }
            });
        }
    }

    public function run(string $instruction):Promise {
        if (!self::$pstdin) {
            return call(function() {});
        }
        return self::$pstdin->write($instruction);
    }

    public function clear():Promise {
        return $this->run('reset;'.\PHP_EOL);
    }

    public function echo(string $data):Promise {
        return $this->run('echo "'.$data.'";'.\PHP_EOL);
    }
}
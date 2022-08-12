<?php
namespace CatPaw\CUI;

use function CatPaw\CUI\Colors\nocolor;

use CatPaw\CUI\Utilities\Interval;

function send(string $instruction) {
    return Engine::send($instruction);
}

function clear() {
    return \join([nocolor(),"\033c"]);
}

interface RenderConfiguration {
    public function setInterval(Interval $interval):void;
}

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
 * Check if key is active.
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
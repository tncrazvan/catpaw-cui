<?php
namespace CatPaw\CUI;

use function Amp\call;

function send(string $instruction) {
    Stream::initialize();
    return Stream::send($instruction);
}

function clear() {
    Stream::initialize();
    return call(function() {
        yield send(NOCOLOR);
        return yield Stream::send("\033c");
    });
}
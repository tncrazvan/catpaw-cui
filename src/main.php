<?php
namespace {

    use function Amp\delay;

    use function CatPaw\CUI\Colors\foreground;
    use function CatPaw\CUI\Components\box;

    use CatPaw\CUI\Services\CharacterService;

    function main(
        CharacterService $char
    ) {
        while (true) {
            yield $char->send("\033c");
            yield $char->send(foreground(255, 0, 0));
            yield $char->send(box("hello world"));
            yield $char->send(box("hello world"));
            yield $char->send(box("hello world"));
            yield $char->send(NOCOLOR);
            yield delay(100);
        }
    }
}
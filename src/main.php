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
            $text = "hello world!";

            yield $char->send(foreground(255, 0, 0));
            yield $char->send(box("hello world"));
            yield delay(30);
            yield $char->send("\033c");
        }
    }
}
<?php
namespace {

    use function CatPaw\CUI\Colors\background;

    use function CatPaw\CUI\Colors\foreground;
    use function CatPaw\CUI\Colors\nocolor;

    use CatPaw\CUI\Services\ExecutorService;

    function main(
        ExecutorService $ex
    ) {
        yield $ex->run("reset");
        yield $ex->echo(join([
            PHP_EOL,
            PHP_EOL,
            foreground(255, 0, 0),
            background(0, 200, 0),
            "hello world",
            nocolor(),
            PHP_EOL,
            PHP_EOL,
        ]));
    }
}
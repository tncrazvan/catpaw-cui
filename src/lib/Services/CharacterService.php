<?php
namespace CatPaw\CUI\Services;

use Amp\ByteStream\ResourceOutputStream;
use Amp\Promise;
use CatPaw\Attributes\Service;

#[Service]
class CharacterService {
    private ResourceOutputStream $stdout;

    public function __construct() {
        $this->stdout = new ResourceOutputStream(\STDOUT);
    }

    public function clear() {
        return $this->send("\033c");
    }
    
    public function send(string $instruction):Promise {
        return $this->stdout->write($instruction);
    }
}

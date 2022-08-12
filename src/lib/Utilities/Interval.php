<?php

namespace CatPaw\CUI\Utilities;

use function CatPaw\milliseconds;

class Interval {
    private static array $intervals = [];
    public static function of(int $milliseconds, string $id = ''):self {
        $key = "$id:$milliseconds";
        if (!isset(self::$intervals[$key])) {
            self::$intervals[$key] = new self($milliseconds);
        }
        return self::$intervals[$key];
    }

    private int $t0;
    private function __construct(
        private int $milliseconds
    ) {
        $this->reset();
    }

    public function isDue(bool $reset = true):bool {
        $due = milliseconds() >= $this->t0 + $this->milliseconds;
        if ($due && $reset) {
            $this->reset();
        }
        return $due;
    }

    public function reset() {
        $this->t0 = milliseconds();
    }
}
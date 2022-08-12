<?php

namespace CatPaw\CUI\Utilities;

use function CatPaw\milliseconds;

class Interval {
    private static array $intervals = [];

    /**
     * Make a new interval.
     * If found, it will always return an already existing interval with the same duration ($milliseconds) and $id.
     * @param  int      $milliseconds
     * @param  string   $id
     * @return Interval
     */
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

    /** 
     * Get the value of the interval (milliseconds).
     * @return int
     */
    public function getValue():int {
        return $this->milliseconds;
    }

    /**
     * Check if the timer expired.
     * @param  bool $reset if true, will automatically reset the timer.
     * @return bool true if it expired, false otherwise.
     */
    public function isDue(bool $reset = true):bool {
        $due = milliseconds() >= $this->t0 + $this->milliseconds;
        if ($due && $reset) {
            $this->reset();
        }
        return $due;
    }

    /**
     * Reset the timer.
     * @return void
     */
    public function reset() {
        $this->t0 = milliseconds();
    }
}
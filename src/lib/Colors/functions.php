<?php
namespace CatPaw\CUI\Colors;

/**
 * @return callable(int $pwidth, int $pheight):string
 */
function nocolor():callable {
    return fn(int $pwidth, int $pheight):string => "\033[0m";
}

/**
 * @param int $red
 * @param int $green
 * @param int $blue
 * @return callable(int $pwidth, int $pheight):string
 */
function background(
    int $red = 0,
    int $green = 0,
    int $blue = 0,
):callable {
    $red   %= 256;
    $green %= 256;
    $blue  %= 256;
    return fn(int $pwidth, int $pheight):string => "\033[48;2;{$red};{$green};{$blue}m";
}

/**
 * @param int $red
 * @param int $green
 * @param int $blue
 * @return callable(int $pwidth, int $pheight):string
 */
function foreground(
    int $red = 0,
    int $green = 0,
    int $blue = 0,
):callable {
    $red   %= 256;
    $green %= 256;
    $blue  %= 256;
    return fn(int $pwidth, int $pheight):string => "\033[38;2;{$red};{$green};{$blue}m";
}

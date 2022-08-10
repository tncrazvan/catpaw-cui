<?php
namespace CatPaw\CUI\Colors;

\define("NOCOLOR", "\033[0m");

function background(
    int $red = 0,
    int $green = 0,
    int $blue = 0,
):string {
    $red   %= 256;
    $green %= 256;
    $blue  %= 256;
    return "\033[48;2;{$red};{$green};{$blue}2m";
}

function foreground(
    int $red = 0,
    int $green = 0,
    int $blue = 0,
):string {
    $red   %= 256;
    $green %= 256;
    $blue  %= 256;
    return "\033[38;2;{$red};{$green};{$blue}2m";
}

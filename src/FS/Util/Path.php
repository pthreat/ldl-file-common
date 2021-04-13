<?php declare(strict_types=1);

namespace LDL\FS\Util;

class Path
{
    public static function make(...$args) : string
    {
        return implode(\DIRECTORY_SEPARATOR, $args);
    }
}

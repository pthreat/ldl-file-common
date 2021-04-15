<?php declare(strict_types=1);

namespace LDL\File\Util;

abstract class FileHelper
{
    public static function createPath(...$args) : string
    {
        return implode(\DIRECTORY_SEPARATOR, $args);
    }
}

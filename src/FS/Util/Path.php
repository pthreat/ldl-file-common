<?php

namespace LDL\FS\Util;

class Path{

    public static function make() : string
    {
        return implode(\DIRECTORY_SEPARATOR, func_get_args());
    }

}

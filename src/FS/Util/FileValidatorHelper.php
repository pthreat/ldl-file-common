<?php declare(strict_types=1);

namespace LDL\FS\Util;

abstract class FileValidatorHelper
{
    public static function getFilename($value, bool $convertSplFileInfoToString=true) : ?string
    {
        if(is_string($value)){
            return $value;
        }

        if($convertSplFileInfoToString && is_object($value)){

            if($value instanceof \SplFileInfo){
                return (string) $value->getRealPath();
            }

            $methods = array_map('strtolower', get_class_methods($value));

            if(in_array('__tostring', $methods, true)){
                $value = (string) $value;
            }

        }

        if(is_string($value)) {
            return $value;
        }

        $msg = sprintf(
            '"%s" only accepts string path\'s to files, or "%s" objects, or objects which a __toString method',
            __CLASS__,
            \SplFileInfo::class
        );

        throw new \InvalidArgumentException($msg);
    }
}

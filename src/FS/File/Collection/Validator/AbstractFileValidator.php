<?php declare(strict_types=1);

namespace LDL\FS\File\Collection\Validator;

use LDL\Type\Collection\Interfaces\Validation\ValueValidatorInterface;

abstract class AbstractFileValidator implements ValueValidatorInterface
{
    private $convertSplFileInfoToString;

    public function __construct(bool $convertSplFileInfoToString=true)
    {
        $this->convertSplFileInfoToString = $convertSplFileInfoToString;
    }

    protected function getFilename($value) : ?string
    {
        if(is_string($value)){
            return $value;
        }

        if($this->convertSplFileInfoToString && is_object($value)){

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

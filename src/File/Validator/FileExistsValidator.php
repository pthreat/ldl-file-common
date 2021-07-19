<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorDescriptionTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorInterface;

class FileExistsValidator implements ValidatorInterface, NegatedValidatorInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;
    use ValidatorDescriptionTrait;

    private const DESCRIPTION = 'Validate that a file exist';

    public function __construct(
        bool $negated=false,
        string $description=null
    )
    {
        $this->_tNegated = $negated;
        $this->_tDescription = $description ?? self::DESCRIPTION;
    }

    public function assertTrue($path): void
    {
        if(file_exists($path)){
            return;
        }

        $msg = "File \"$path\" does not exists";
        throw new Exception\FileNotFoundException($msg);
    }

    public function assertFalse($path): void
    {
        if(!file_exists($path)){
            return;
        }

        $msg = "File \"$path\" must NOT exists";
        throw new Exception\FileNotFoundException($msg);
    }
}

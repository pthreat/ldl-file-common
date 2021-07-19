<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorDescriptionTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorInterface;

class ReadableFileValidator implements ValidatorInterface, NegatedValidatorInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;
    use ValidatorDescriptionTrait;

    private const DESCRIPTION = 'Validate that a file is readable';

    public function __construct(bool $negated=false, string $description=null)
    {
        $this->_tNegated = $negated;
        $this->_tDescription = $description ?? self::DESCRIPTION;
    }

    public function assertTrue($path): void
    {
        if(is_readable($path)){
            return;
        }

        $msg = "File \"$path\" is not readable!\n";
        throw new Exception\ReadableFileValidatorException($msg);
    }

    public function assertFalse($path): void
    {
        if(!is_readable($path)){
            return;
        }

        $msg = "File \"$path\" is readable!\n";
        throw new Exception\ReadableFileValidatorException($msg);
    }
}

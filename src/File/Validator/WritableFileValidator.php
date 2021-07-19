<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorDescriptionTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorInterface;

class WritableFileValidator implements ValidatorInterface, NegatedValidatorInterface
{
    use ValidatorValidateTrait {validate as _validate;}
    use NegatedValidatorTrait;
    use ValidatorDescriptionTrait;

    private const DESCRIPTION = 'Validate that a file is writable';

    public function __construct(bool $negated=false, string $description=null)
    {
        $this->_tNegated = $negated;
        $this->_tDescription = $description ?? self::DESCRIPTION;
    }

    /**
     * @param mixed $path
     * @throws Exception\FileNotFoundException
     */
    public function validate($path): void
    {
        if(!file_exists($path)){
            $msg = "File \"$path\" does not exists";
            throw new Exception\FileNotFoundException($msg);
        }

        $this->_validate($path);
    }

    public function assertTrue($path): void
    {
        if(is_writable($path)){
            return;
        }

        $msg = "File \"$path\" is not writable!\n";
        throw new Exception\WritableFileValidatorException($msg);
    }

    public function assertFalse($path): void
    {
        if(!is_writable($path)){
            return;
        }

        $msg = "File \"$path\" is writable!\n";
        throw new Exception\WritableFileValidatorException($msg);
    }
}

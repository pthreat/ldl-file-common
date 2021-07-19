<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorDescriptionTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorInterface;

class JsonFileValidator implements ValidatorInterface, NegatedValidatorInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;
    use ValidatorDescriptionTrait;

    private const DESCRIPTION = 'Validate that a file is a json file';

    public function __construct(bool $negated=false, string $description=null)
    {
        $this->_tNegated = $negated;
        $this->_tDescription = $description ?? self::DESCRIPTION;
    }

    public function assertTrue($path): void
    {
        $content = file_get_contents($path);

        try {

            json_decode($content, false, 2048, \JSON_THROW_ON_ERROR);

        }catch (\Exception $e){

            $msg = sprintf(
                'Could not decode file "%s" as JSON, Decode error: %s',
                $path,
                $e->getMessage()
            );

            throw new Exception\JsonFileDecodeException($msg);
        }
    }

    public function assertFalse($path): void
    {
        $content = file_get_contents($path);

        try {

            json_decode($content, false, 2048, \JSON_THROW_ON_ERROR);

            $msg = sprintf(
                'Could decode file "%s" as JSON',
                $path
            );

            throw new Exception\JsonFileDecodeException($msg);

        }catch (\Exception $e){

        }
    }
}

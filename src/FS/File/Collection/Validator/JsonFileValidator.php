<?php declare(strict_types=1);

namespace LDL\FS\File\Collection\Validator;

use LDL\Type\Collection\Interfaces\CollectionInterface;
use LDL\Type\Collection\Interfaces\Validation\AppendItemValidatorInterface;

class JsonFileValidator extends AbstractFileValidator implements AppendItemValidatorInterface
{
    public function validate(CollectionInterface $collection, $item, $key): void
    {
        $file = $this->getFilename($item);

        try {

            $content = file_get_contents($file);
            json_decode($content, false, 2048, \JSON_THROW_ON_ERROR);

        }catch (\Exception $e){

            $msg = sprintf(
                'Could not decode file "%s" as JSON, Decode error: %s',
                $item,
                $e->getMessage()
            );

            throw new Exception\JsonFileDecodeException($msg);

        }
    }
}

<?php declare(strict_types=1);

namespace LDL\FS\File\Collection\Validator;

use LDL\Type\Collection\Interfaces\CollectionInterface;
use LDL\Type\Collection\Interfaces\Validation\AppendItemValidatorInterface;

class FileExistsValidator extends AbstractFileValidator implements AppendItemValidatorInterface
{
    public function validateValue(CollectionInterface $collection, $item, $key): void
    {
        $item = $this->getFilename($item);

        if(file_exists($item)){
            return;
        }

        $msg = "File \"$item\" does not exists";
        throw new Exception\FileNotFoundException($msg);
    }
}

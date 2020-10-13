<?php declare(strict_types=1);

namespace LDL\FS\File\Collection;

use LDL\FS\File\Collection\Validator\ReadableFileValidator;
use LDL\FS\File\Collection\Validator\WritableFileValidator;
use LDL\Type\Collection\Types\Object\ObjectCollection;

class ReadWriteFileCollection extends ObjectCollection
{

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);
        $this->getValidatorChain()
            ->append(new ReadableFileValidator())
            ->append(new WritableFileValidator());
    }

}
<?php declare(strict_types=1);

namespace LDL\FS\File\Collection;

use LDL\FS\File\Collection\Validator\ReadableFileValidator;
use LDL\Type\Collection\Types\Object\ObjectCollection;

final class ReadableFileCollection extends ObjectCollection
{

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);
        $this->getValidatorChain()
            ->append(new ReadableFileValidator())
            ->lock();
    }

}
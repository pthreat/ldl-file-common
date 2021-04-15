<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Validator\FileExistsValidator;
use LDL\File\Validator\WritableFileValidator;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Type\Collection\Types\Object\ObjectCollection;


final class WritableFileCollection extends ObjectCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);

        $this->getAppendValueValidatorChain()
            ->append(new FileExistsValidator())
            ->append(new WritableFileValidator())
            ->lock();
    }
}
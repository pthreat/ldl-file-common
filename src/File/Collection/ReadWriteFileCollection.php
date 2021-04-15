<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Validator\FileExistsValidator;
use LDL\File\Validator\ReadableFileValidator;
use LDL\File\Validator\WritableFileValidator;
use LDL\Type\Collection\AbstractCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;

final class ReadWriteFileCollection extends AbstractCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);
        $this->getAppendValueValidatorChain()
            ->append(new FileExistsValidator())
            ->append(new ReadableFileValidator())
            ->append(new WritableFileValidator())
            ->lock();
    }
}
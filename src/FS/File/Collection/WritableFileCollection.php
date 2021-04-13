<?php declare(strict_types=1);

namespace LDL\FS\File\Collection;

use LDL\FS\File\Collection\Validator\FileExistsValidator;
use LDL\FS\File\Collection\Validator\WritableFileValidator;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Validators\ClassComplianceValidator;

final class WritableFileCollection extends ObjectCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);

        $this->getAppendValueValidatorChain()
            ->append(new ClassComplianceValidator(\SplFileInfo::class))
            ->append(new FileExistsValidator())
            ->append(new WritableFileValidator())
            ->lock();
    }
}
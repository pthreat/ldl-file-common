<?php declare(strict_types=1);

namespace LDL\FS\File\Collection;

use LDL\FS\File\Collection\Validator\FileExistsValidator;
use LDL\FS\File\Collection\Validator\JsonFileValidator;
use LDL\FS\File\Collection\Validator\ReadableFileValidator;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Validators\ClassComplianceValidator;

class JsonFileCollection extends ObjectCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);

        $this->getAppendValueValidatorChain()
            ->append(new ClassComplianceValidator(\SplFileInfo::class))
            ->append(new FileExistsValidator())
            ->append(new ReadableFileValidator())
            ->append(new JsonFileValidator())
            ->lock();
    }
}

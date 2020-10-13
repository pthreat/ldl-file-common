<?php declare(strict_types=1);

namespace LDL\FS\Type\Types\Generic\Collection;

use LDL\FS\Type\Interfaces\FileTypeInterface;
use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Type\Collection\Types\Object\Validator\InterfaceComplianceItemValidator;

class GenericFileCollection extends ObjectCollection
{
    public function __construct(iterable $items = null)
    {
        parent::__construct($items);
        $this->getValidatorChain()
            ->append(new InterfaceComplianceItemValidator(FileTypeInterface::class));
    }
}

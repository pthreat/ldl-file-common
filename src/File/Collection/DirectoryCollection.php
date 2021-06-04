<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Validator\Config\FileTypeValidatorConfig;
use LDL\File\Validator\FileTypeValidator;
use LDL\Type\Collection\AbstractCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;

final class DirectoryCollection extends AbstractCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);

        $this->getAppendValueValidatorChain()
            ->append(new FileTypeValidator([FileTypeValidatorConfig::FILE_TYPE_DIRECTORY]))
            ->lock();
    }
}

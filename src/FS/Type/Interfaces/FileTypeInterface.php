<?php declare(strict_types=1);

namespace LDL\FS\Type\Interfaces;

use LDL\FS\Type\Exception\FileNotFoundException;
use LDL\FS\Type\Exception\PermissionDeniedException;

interface FileTypeInterface
{
    /**
     * @return int
     * @throws FileNotFoundException
     * @throws PermissionDeniedException
     */
    public function lineCount() : int;
}

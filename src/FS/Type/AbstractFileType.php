<?php declare(strict_types=1);

namespace LDL\FS\Type;

use LDL\FS\Type\Exception\FileNotFoundException;
use LDL\FS\Type\Exception\PermissionDeniedException;

abstract class AbstractFileType extends \SplFileInfo implements Interfaces\FileTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function lineCount(): int
    {
        $file = $this->getRealPath();

        if(false === $file){
            $msg = sprintf(
                'File "%s%s%s" does not exists',
                $this->getPath(),
                \DIRECTORY_SEPARATOR,
                $this->getFilename()
            );

            throw new FileNotFoundException($msg);
        }

        if(!is_readable($file)){
            $msg = "{$file} is not readable";
            throw new PermissionDeniedException($msg);
        }

        $fp = fopen($this->getRealPath(), 'rb');
        $lines = 1;

        while (!feof($fp)) {
            $lines += substr_count(fread($fp, 8192), \PHP_EOL);
        }

        fclose($fp);

        return $lines;
    }

}

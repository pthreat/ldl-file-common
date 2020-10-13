<?php declare(strict_types=1);

require '../vendor/autoload.php';

use LDL\FS\Type\Types\Generic\GenericFileType;

$file = new GenericFileType(__FILE__);

echo "This file contains {$file->lineCount()} lines".PHP_EOL;


echo "Output must be equal to ". __LINE__. ' lines';
<?php
namespace LDL\FS\Type\Types\Generic\Collection;

use LDL\Type\Exception\TypeMismatchException;
use LDL\Type\Collection\Types\Object\ObjectCollection;

use LDL\FS\Type\Interfaces\FileTypeInterface;

class GenericFileCollection extends ObjectCollection
{

    public function validateItem($item) : void
    {
        parent::validateItem($item);


        if($item instanceof FileTypeInterface){
            return;
        }

        $msg = sprintf(
            'Expected value must be an instance of %s, instance of "%s" was given',
            FileTypeInterface::class,
	    get_class($item)
        );

        throw new TypeMismatchException($msg);
    }

}

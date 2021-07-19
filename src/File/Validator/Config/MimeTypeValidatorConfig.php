<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Type\Collection\Types\String\StringCollection;
use LDL\Validators\Config\Traits\ValidatorConfigTrait;
use LDL\Validators\Config\ValidatorConfigInterface;

class MimeTypeValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigTrait;

    /**
     * @var StringCollection
     */
    private $types;

    public function __construct(
        $types
    )
    {
        if(count($types) === 0){
            throw new \InvalidArgumentException('The collection must have at least one mime type');
        }

        $this->types = new StringCollection($types);
    }

    public function getTypes(): StringCollection
    {
        return $this->types;
    }

    /**
     * @param array $data
     * @return ArrayFactoryInterface
     * @throws ArrayFactoryException
     */
    public static function fromArray(array $data = []): ArrayFactoryInterface
    {
        if(false === array_key_exists('types', $data)){
            $msg = sprintf("Missing property 'types' in %s", __CLASS__);
            throw new ArrayFactoryException($msg);
        }

        try{
            return new self(
                $data['types']
            );
        }catch(\Exception $e){
            throw new ArrayFactoryException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'types' => $this->types->toArray()
        ];
    }
}
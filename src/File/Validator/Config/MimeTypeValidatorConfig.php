<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Type\Collection\Types\String\StringCollection;
use LDL\Validators\Config\NegatedValidatorConfigInterface;
use LDL\Validators\Config\Traits\NegatedValidatorConfigTrait;
use LDL\Validators\Config\Traits\ValidatorConfigTrait;
use LDL\Validators\Config\ValidatorConfigInterface;

class MimeTypeValidatorConfig implements ValidatorConfigInterface, NegatedValidatorConfigInterface
{
    use ValidatorConfigTrait;
    use NegatedValidatorConfigTrait;

    /**
     * @var StringCollection
     */
    private $types;

    public function __construct(
        $types,
        bool $negated=false,
        bool $dumpable=true,
        string $description=null
    )
    {
        if(count($types) === 0){
            throw new \InvalidArgumentException('The collection must have at least one mime type');
        }

        $this->types = new StringCollection($types);
        $this->_tNegated = $negated;
        $this->_tDumpable = $dumpable;
        $this->_tDescription = $description;
    }

    public function getTypes(): StringCollection
    {
        return $this->types;
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
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
                $data['types'],
                array_key_exists('negated', $data) ? (bool)$data['negated'] : false,
                array_key_exists('dumpable', $data) ? (bool)$data['dumpable'] : true,
                array_key_exists('description', $data) ? (string)$data['description'] : null,
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
            'types' => $this->types->toArray(),
            'negated' => $this->_tNegated,
            'dumpable' => $this->_tDumpable,
            'description' => $this->_tDescription
        ];
    }
}
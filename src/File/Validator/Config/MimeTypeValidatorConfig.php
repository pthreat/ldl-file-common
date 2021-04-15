<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Type\Collection\Types\String\StringCollection;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\Config\ValidatorConfigInterfaceTrait;

class MimeTypeValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigInterfaceTrait;

    /**
     * @var StringCollection
     */
    private $types;

    /**
     * @var bool
     */
    private $match;

    public function __construct($types, bool $match=true, bool $strict = true)
    {
        if(count($types) === 0){
            throw new \InvalidArgumentException('The collection must have at least one mime type');
        }

        $this->match = $match;
        $this->types = new StringCollection($types);
        $this->_isStrict = $strict;
    }

    public function getTypes(): StringCollection
    {
        return $this->types;
    }

    public function isMatch() : bool
    {
        return $this->match;
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
                array_key_exists('match', $data) ? (bool)$data['match'] : true,
                array_key_exists('strict', $data) ? (bool)$data['strict'] : true
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
            'match' => $this->match,
            'strict' => $this->_isStrict
        ];
    }
}
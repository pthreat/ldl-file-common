<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Type\Collection\Types\String\UniqueStringCollection;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\Config\ValidatorConfigInterfaceTrait;

class FileTypeValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigInterfaceTrait;

    public const FILE_TYPE_REGULAR='regular';
    public const FILE_TYPE_DIRECTORY='directory';
    public const FILE_TYPE_LINK='link';
    public const FILE_TYPE_SOCKET='socket';
    public const FILE_TYPE_FIFO='fifo';
    public const FILE_TYPE_CHAR='char';
    public const FILE_TYPE_BLOCK='block';
    public const FILE_TYPE_UNKNOWN='unknown';

    /**
     * @var UniqueStringCollection
     */
    private $types;

    /**
     * @var bool
     */
    private $match;

    public function __construct(
        iterable $types,
        bool $match=true,
        bool $strict = true
    )
    {
        $validTypes = new UniqueStringCollection([
            self::FILE_TYPE_DIRECTORY,
            self::FILE_TYPE_REGULAR,
            self::FILE_TYPE_LINK,
            self::FILE_TYPE_SOCKET,
            self::FILE_TYPE_FIFO,
            self::FILE_TYPE_CHAR,
            self::FILE_TYPE_BLOCK,
            self::FILE_TYPE_UNKNOWN
        ]);

        $types = new UniqueStringCollection($types);

        if(count($types) === 0){
            throw new \InvalidArgumentException(
                'At least one the following file types must be specified: "%s"',
                $validTypes->implode(', ')
            );
        }

        $types->map(static function($item) use ($validTypes){
            if($validTypes->hasValue($item)){
                return $item;
            }

            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid file type specified "%s", valid file types are: "%s"',
                    $item,
                    $validTypes->implode(', ')
                )
            );
        });

        $this->match = $match;
        $this->types = $types;
        $this->_isStrict = $strict;
    }

    /**
     * @return bool
     */
    public function isMatch() : bool
    {
        return $this->match;
    }

    /**
     * @return UniqueStringCollection
     */
    public function getTypes(): UniqueStringCollection
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

        if(!is_iterable($data['types'])){
            $msg = sprintf("'types' property is not iterable in %s", __CLASS__);
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
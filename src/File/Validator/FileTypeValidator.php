<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Type\Collection\Types\String\UniqueStringCollection;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileTypeValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;

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
     * @var string|null
     */
    private $description;

    public function __construct(iterable $types, bool $negated=false, string $description=null)
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

        $this->types = $types;
        $this->_tNegated = $negated;
        $this->description = $description;
    }

    /**
     * @return UniqueStringCollection
     */
    public function getTypes(): UniqueStringCollection
    {
        return $this->types;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File type must be one of: %s',
                implode(",", $this->types->toArray())
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $type = $this->initialValidation($path);

        if($this->types->hasValue($type)){
            return;
        }

        throw new \InvalidArgumentException('File type criteria not satisfied');
    }

    public function assertFalse($path): void
    {
        $type = $this->initialValidation($path);

        if(!$this->types->hasValue($type)){
            return;
        }

        throw new \InvalidArgumentException('File type criteria not satisfied');
    }

    public function jsonSerialize(): array
    {
        return $this->getConfig();
    }

    /**
     * @param array $data
     * @return ValidatorInterface
     * @throws Exception\FileValidatorException
     */
    public static function fromConfig(array $data = []): ValidatorInterface
    {
        if(false === array_key_exists('types', $data)){
            $msg = sprintf("Missing property 'types' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        if(!is_iterable($data['types'])){
            $msg = sprintf("'types' property is not iterable in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        try{
            return new self(
                $data['types'],
                array_key_exists('negated', $data) ? (bool)$data['negated'] : false,
                $data['description'] ?? null
            );
        }catch(\Exception $e){
            throw new Exception\FileValidatorException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'types' => $this->types->toArray(),
            'negated' => $this->_tNegated,
            'description' => $this->getDescription()
        ];
    }

    private function initialValidation($path): string
    {
        $perms = fileperms($path);

        if(!$perms){
            throw new \InvalidArgumentException('Invalid file provided');
        }

        switch ($perms & 0xF000) {
            case 0xC000: // socket
                $type = self::FILE_TYPE_SOCKET;
                break;
            case 0xA000: // symbolic link
                $type = self::FILE_TYPE_LINK;
                break;
            case 0x8000: // regular
                $type = self::FILE_TYPE_REGULAR;
                break;
            case 0x6000: // block special
                $type = self::FILE_TYPE_BLOCK;
                break;
            case 0x4000: // directory
                $type = self::FILE_TYPE_DIRECTORY;
                break;
            case 0x2000: // character special
                $type = self::FILE_TYPE_CHAR;
                break;
            case 0x1000: // FIFO pipe
                $type = self::FILE_TYPE_FIFO;
                break;
            default: // unknown
                $type = self::FILE_TYPE_UNKNOWN;
        }

        return $type;
    }
}
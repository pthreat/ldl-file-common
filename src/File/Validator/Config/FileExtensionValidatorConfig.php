<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\Config\ValidatorConfigInterfaceTrait;

class FileExtensionValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigInterfaceTrait;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var bool
     */
    private $match;

    public function __construct(
        string $extension,
        bool $match = true,
        bool $strict = true
    )
    {
        $this->extension = $extension;
        $this->match = $match;
        $this->_isStrict = $strict;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return bool
     */
    public function isMatch(): bool
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
        if(false === array_key_exists('extension', $data)){
            $msg = sprintf("Missing property 'extension' in %s", __CLASS__);
            throw new ArrayFactoryException($msg);
        }

        return new self(
            $data['extension'],
            $match = array_key_exists('match', $data) ? (bool)$data['match'] : true,
            array_key_exists('strict', $data) ? (bool)$data['strict'] : true
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'extension' => $this->extension,
            'match' => $this->match,
            'strict' => $this->_isStrict
        ];
    }
}
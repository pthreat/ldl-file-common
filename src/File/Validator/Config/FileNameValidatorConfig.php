<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\Config\ValidatorConfigInterfaceTrait;

class FileNameValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigInterfaceTrait;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var bool
     */
    private $match;

    public function __construct(
        string $filename,
        bool $match = true,
        bool $strict = true
    )
    {
        $this->filename = $filename;
        $this->match = $match;
        $this->_isStrict = $strict;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
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
        if(false === array_key_exists('filename', $data)){
            $msg = sprintf("Missing property 'filename' in %s", __CLASS__);
            throw new ArrayFactoryException($msg);
        }

        return new self(
            $data['filename'],
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
            'filename' => $this->filename,
            'match' => $this->match,
            'strict' => $this->_isStrict
        ];
    }
}
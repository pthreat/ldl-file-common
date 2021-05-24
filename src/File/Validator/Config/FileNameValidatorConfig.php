<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Validators\Config\Traits\ValidatorConfigTrait;
use LDL\Validators\Config\ValidatorConfigInterface;

class FileNameValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigTrait;

    /**
     * @var string
     */
    private $filename;

    public function __construct(
        string $filename,
        bool $negated=false,
        bool $dumpable=true,
        string $description=null
    )
    {
        $this->filename = $filename;
        $this->_tNegated = $negated;
        $this->_tDumpable = $dumpable;
        $this->_tDescription = $description;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
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
            array_key_exists('negated', $data) ? (bool)$data['negated'] : false,
            array_key_exists('dumpable', $data) ? (bool)$data['dumpable'] : true,
            array_key_exists('description', $data) ? (string)$data['description'] : null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'filename' => $this->filename,
            'negated' => $this->_tNegated,
            'dumpable' => $this->_tDumpable,
            'description' => $this->_tDescription
        ];
    }
}
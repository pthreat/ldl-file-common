<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Validators\Config\Traits\ValidatorConfigTrait;
use LDL\Validators\Config\ValidatorConfigInterface;

class FileExtensionValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigTrait;

    /**
     * @var string
     */
    private $extension;

    public function __construct(
        string $extension
    )
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
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
            $data['extension']
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'extension' => $this->extension
        ];
    }
}
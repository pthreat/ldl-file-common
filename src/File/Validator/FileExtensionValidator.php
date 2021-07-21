<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileExtensionValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(
        string $extension,
        bool $negated=false,
        string $description=null
    )
    {
        $this->extension = $extension;
        $this->_tNegated = $negated;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File extension must match with: %s',
                $this->extension
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getExtension() === $this->extension){
            return;
        }

        throw new \LogicException("File: \"$path\" does NOT match criteria");
    }

    public function assertFalse($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getExtension() !== $this->extension){
            return;
        }

        throw new \LogicException("File: \"$path\" match criteria");
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
        if(!array_key_exists('extension', $data)){
            $msg = sprintf("Missing property 'extension' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        return new self(
            $data['extension'],
            array_key_exists('negated', $data) ? (bool)$data['negated'] : false,
            $data['description'] ?? null
        );
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'extension' => $this->extension,
            'negated' => $this->_tNegated,
            'description' => $this->getDescription()
        ];
    }
}
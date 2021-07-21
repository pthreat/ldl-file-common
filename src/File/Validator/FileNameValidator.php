<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileNameValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(
        string $filename,
        bool $negated=false,
        string $description=null
    )
    {
        $this->filename = $filename;
        $this->_tNegated = $negated;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File name must match with: %s',
                $this->filename
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getFilename() === $this->filename){
            return;
        }

        throw new \LogicException("File: \"$path\" does not match criteria");
    }

    public function assertFalse($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getFilename() !== $this->filename){
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
        if(!array_key_exists('filename', $data)){
            $msg = sprintf("Missing property 'filename' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        return new self(
            $data['filename'],
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
            'filename' => $this->filename,
            'negated' => $this->_tNegated,
            'description' => $this->getDescription()
        ];
    }
}
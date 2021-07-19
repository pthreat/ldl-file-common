<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorHasConfigInterfaceTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileExtensionValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use ValidatorHasConfigInterfaceTrait;
    use NegatedValidatorTrait;

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
        $this->_tConfig = new Config\FileExtensionValidatorConfig($extension);
        $this->_tNegated = $negated;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File extension must match with: %s',
                $this->_tConfig->getExtension()
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getExtension() === $this->_tConfig->getExtension()){
            return;
        }

        throw new \LogicException("File: \"$path\" does NOT match criteria");
    }

    public function assertFalse($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getExtension() !== $this->_tConfig->getExtension()){
            return;
        }

        throw new \LogicException("File: \"$path\" match criteria");
    }

    /**
     * @param ValidatorConfigInterface $config
     * @param bool $negated
     * @param string|null $description
     * @return ValidatorInterface
     * @throws \InvalidArgumentException
     */
    public static function fromConfig(ValidatorConfigInterface $config, bool $negated = false, string $description=null): ValidatorInterface
    {
        if(false === $config instanceof Config\FileExtensionValidatorConfig){
            $msg = sprintf(
                'Config expected to be %s, config of class %s was given',
                __CLASS__,
                get_class($config)
            );
            throw new \InvalidArgumentException($msg);
        }

        /**
         * @var Config\FileExtensionValidatorConfig $config
         */
        return new self(
            $config->getExtension(),
            $negated,
            $description
        );
    }
}
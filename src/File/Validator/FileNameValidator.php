<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorHasConfigInterfaceTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileNameValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use ValidatorHasConfigInterfaceTrait;
    use NegatedValidatorTrait;

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
        $this->_tConfig = new Config\FileNameValidatorConfig($filename);
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
                'File name must match with: %s',
                $this->_tConfig->getFilename()
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getFilename() === $this->_tConfig->getFilename()){
            return;
        }

        throw new \LogicException("File: \"$path\" does not match criteria");
    }

    public function assertFalse($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getFilename() !== $this->_tConfig->getFilename()){
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
        if(false === $config instanceof Config\FileNameValidatorConfig){
            $msg = sprintf(
                'Config expected to be %s, config of class %s was given',
                __CLASS__,
                get_class($config)
            );
            throw new \InvalidArgumentException($msg);
        }

        /**
         * @var Config\FileNameValidatorConfig $config
         */
        return new self(
            $config->getFilename(),
            $negated,
            $description
        );
    }
}
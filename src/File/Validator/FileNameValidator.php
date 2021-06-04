<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorInterface;

class FileNameValidator implements ValidatorInterface, NegatedValidatorInterface
{
    use ValidatorValidateTrait;

    /**
     * @var Config\FileNameValidatorConfig
     */
    private $config;

    public function __construct(string $filename, bool $negated=false, bool $dumpable=true, string $description=null)
    {
        $this->config = new Config\FileNameValidatorConfig($filename, $negated, $dumpable, $description);
    }

    public function assertTrue($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getFilename() === $this->config->getFilename()){
            return;
        }

        throw new \LogicException("File: \"$path\" does not match criteria");
    }

    public function assertFalse($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getFilename() !== $this->config->getFilename()){
            return;
        }

        throw new \LogicException("File: \"$path\" match criteria");
    }

    /**
     * @param ValidatorConfigInterface $config
     * @return ValidatorInterface
     * @throws \InvalidArgumentException
     */
    public static function fromConfig(ValidatorConfigInterface $config): ValidatorInterface
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
            $config->isNegated(),
            $config->isDumpable()
        );
    }

    /**
     * @return Config\FileNameValidatorConfig
     */
    public function getConfig(): Config\FileNameValidatorConfig
    {
        return $this->config;
    }
}
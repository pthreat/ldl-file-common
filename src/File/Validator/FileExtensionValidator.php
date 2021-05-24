<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileExtensionValidator implements ValidatorInterface
{
    /**
     * @var Config\FileExtensionValidatorConfig
     */
    private $config;

    public function __construct(
        string $extension,
        bool $negated=false,
        bool $dumpable=true,
        string $description=null
    )
    {
        $this->config = new Config\FileExtensionValidatorConfig($extension, $negated, $dumpable, $description);
    }

    /**
     * @param mixed $path
     * @throws \Exception
     */
    public function validate($path): void
    {
        $this->config->isNegated() ? $this->assertFalse($path) : $this->assertTrue($path);
    }

    public function assertTrue($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getExtension() === $this->config->getExtension()){
            return;
        }

        throw new \LogicException("File: \"$path\" does NOT match criteria");
    }

    public function assertFalse($path): void
    {
        $file = new \SplFileInfo($path);

        if($file->getExtension() !== $this->config->getExtension()){
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
            $config->isNegated(),
            $config->isDumpable(),
            $config->getDescription()
        );
    }

    /**
     * @return Config\FileExtensionValidatorConfig
     */
    public function getConfig(): Config\FileExtensionValidatorConfig
    {
        return $this->config;
    }
}
<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\HasValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileExtensionValidator implements ValidatorInterface, HasValidatorConfigInterface
{
    /**
     * @var Config\FileExtensionValidatorConfig
     */
    private $config;

    public function __construct(string $extension, bool $match = true, bool $strict = true)
    {
        $this->config = new Config\FileExtensionValidatorConfig($extension, $match, $strict);
    }

    /**
     * @param string $path
     * @param null $key
     * @param CollectionInterface|null $collection
     * @throws \LogicException
     */
    public function validate($path, $key = null, CollectionInterface $collection = null): void
    {
        $file = new \SplFileInfo($path);
        $sameExtension = $file->getExtension() === $this->config->getExtension();

        if($sameExtension && $this->config->isMatch()){
            return;
        }

        if(!$sameExtension && !$this->config->isMatch()){
            return;
        }

        throw new \LogicException("File: \"$path\" does not match criteria");
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
            $config->isMatch(),
            $config->isStrict()
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
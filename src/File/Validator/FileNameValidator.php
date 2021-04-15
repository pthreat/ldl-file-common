<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\HasValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileNameValidator implements ValidatorInterface, HasValidatorConfigInterface
{
    /**
     * @var Config\FileNameValidatorConfig
     */
    private $config;

    public function __construct(string $filename, bool $match = true, bool $strict = true)
    {
        $this->config = new Config\FileNameValidatorConfig($filename, $match, $strict);
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
        $sameFilename = $file->getFilename() === $this->config->getFilename();

        if($sameFilename && $this->config->isMatch()){
            return;
        }

        if(!$sameFilename && !$this->config->isMatch()){
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
            $config->isMatch(),
            $config->isStrict()
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
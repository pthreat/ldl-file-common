<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Validators\Config\BasicValidatorConfig;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\HasValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class ReadableFileValidator implements ValidatorInterface, HasValidatorConfigInterface
{
    /**
     * @var BasicValidatorConfig
     */
    private $config;

    public function __construct(bool $strict = true)
    {
        $this->config = new BasicValidatorConfig($strict);
    }

    /**
     * @param string $path
     * @param null $key
     * @param CollectionInterface|null $collection
     * @throws Exception\ReadableFileValidatorException
     */
    public function validate($path, $key = null, CollectionInterface $collection = null): void
    {
        if(is_readable($path)){
            return;
        }

        $msg = "File \"$path\" is not readable!\n";
        throw new Exception\ReadableFileValidatorException($msg);
    }

    /**
     * @param ValidatorConfigInterface $config
     * @return ValidatorInterface
     * @throws \InvalidArgumentException
     */
    public static function fromConfig(ValidatorConfigInterface $config): ValidatorInterface
    {
        if(false === $config instanceof BasicValidatorConfig){
            $msg = sprintf(
                'Config expected to be %s, config of class %s was given',
                __CLASS__,
                get_class($config)
            );
            throw new \InvalidArgumentException($msg);
        }

        /**
         * @var BasicValidatorConfig $config
         */
        return new self($config->isStrict());
    }

    /**
     * @return BasicValidatorConfig
     */
    public function getConfig(): BasicValidatorConfig
    {
        return $this->config;
    }
}

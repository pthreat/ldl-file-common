<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\BasicValidatorConfig;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class ReadableFileValidator implements ValidatorInterface
{
    /**
     * @var BasicValidatorConfig
     */
    private $config;

    public function __construct(bool $negated=false, bool $dumpable=true)
    {
        $this->config = new BasicValidatorConfig($negated, $dumpable);
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
        if(is_readable($path)){
            return;
        }

        $msg = "File \"$path\" is not readable!\n";
        throw new Exception\ReadableFileValidatorException($msg);
    }

    public function assertFalse($path): void
    {
        if(!is_readable($path)){
            return;
        }

        $msg = "File \"$path\" is readable!\n";
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
        return new self($config->isNegated(), $config->isDumpable());
    }

    /**
     * @return BasicValidatorConfig
     */
    public function getConfig(): BasicValidatorConfig
    {
        return $this->config;
    }
}

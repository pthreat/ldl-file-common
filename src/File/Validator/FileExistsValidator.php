<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\BasicValidatorConfig;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileExistsValidator implements ValidatorInterface
{
    /**
     * @var BasicValidatorConfig
     */
    private $config;

    public function __construct(
        bool $negated=false,
        bool $dumpable=true,
        string $description=null
    )
    {
        $this->config = new BasicValidatorConfig($negated, $dumpable, $description);
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
        if(file_exists($path)){
            return;
        }

        $msg = "File \"$path\" does not exists";
        throw new Exception\FileNotFoundException($msg);
    }

    public function assertFalse($path): void
    {
        if(!file_exists($path)){
            return;
        }

        $msg = "File \"$path\" must NOT exists";
        throw new Exception\FileNotFoundException($msg);
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
        return new self($config->isNegated(), $config->isDumpable(), $config->getDescription());
    }

    /**
     * @return BasicValidatorConfig
     */
    public function getConfig(): BasicValidatorConfig
    {
        return $this->config;
    }
}

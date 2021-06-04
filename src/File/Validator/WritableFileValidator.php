<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\BasicValidatorConfig;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorInterface;

class WritableFileValidator implements ValidatorInterface, NegatedValidatorInterface
{
    use ValidatorValidateTrait {validate as _validate;}

    /**
     * @var BasicValidatorConfig
     */
    private $config;

    public function __construct(bool $negated=false, bool $dumpable=true, string $description=null)
    {
        $this->config = new BasicValidatorConfig($negated, $dumpable, $description);
    }

    public function assertTrue($path): void
    {
        $this->initialValidation($path);

        if(is_writable($path)){
            return;
        }

        $msg = "File \"$path\" is not writable!\n";
        throw new Exception\WritableFileValidatorException($msg);
    }

    public function assertFalse($path): void
    {
        $this->initialValidation($path);

        if(!is_writable($path)){
            return;
        }

        $msg = "File \"$path\" is writable!\n";
        throw new Exception\WritableFileValidatorException($msg);
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

    /**
     * @param $path
     * @throws Exception\FileNotFoundException
     */
    private function initialValidation($path): void
    {
        if(file_exists($path)){
            return;
        }

        $msg = "File \"$path\" does not exists";
        throw new Exception\FileNotFoundException($msg);
    }
}

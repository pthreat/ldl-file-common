<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\File\Validator\Exception\FileValidatorException;
use LDL\Validators\Config\BasicValidatorConfig;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class DirectoryValidator implements ValidatorInterface
{
    /**
     * @var BasicValidatorConfig
     */
    private $config;

    public function __construct(bool $negated=false, bool $dumpable=true, string $description=null)
    {
        $this->config = new BasicValidatorConfig($negated, $dumpable, $description);
    }

    /**
     * @param mixed $path
     * @throws FileValidatorException
     * @throws Exception\FileNotFoundException
     */
    public function validate($path): void
    {
        $path = realpath($path);

        if(false === $path){
            throw new Exception\FileValidatorException('Provided file is not a valid file');
        }

        $this->config->isNegated() ? $this->assertFalse($path) : $this->assertTrue($path);
    }

    /**
     * @param $path
     * @throws Exception\FileNotFoundException
     */
    public function assertTrue($path): void
    {
        if(is_dir($path)){
            return;
        }

        $msg = "File \"$path\" is not a directory";
        throw new Exception\FileNotFoundException($msg);
    }

    /**
     * @param $path
     * @throws Exception\FileNotFoundException
     */
    public function assertFalse($path): void
    {
        if(!is_dir($path)){
            return;
        }

        $msg = "File \"$path\" is a directory";
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

<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Validators\Config\BasicValidatorConfig;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class JsonFileValidator implements ValidatorInterface
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
     * @throws \Exception
     */
    public function validate($path): void
    {
        $this->config->isNegated() ? $this->assertFalse($path) : $this->assertTrue($path);
    }

    public function assertTrue($path): void
    {
        $content = file_get_contents($path);

        try {

            json_decode($content, false, 2048, \JSON_THROW_ON_ERROR);

        }catch (\Exception $e){

            $msg = sprintf(
                'Could not decode file "%s" as JSON, Decode error: %s',
                $path,
                $e->getMessage()
            );

            throw new Exception\JsonFileDecodeException($msg);
        }
    }

    public function assertFalse($path): void
    {
        $content = file_get_contents($path);

        try {

            json_decode($content, false, 2048, \JSON_THROW_ON_ERROR);

            $msg = sprintf(
                'Could decode file "%s" as JSON',
                $path
            );

            throw new Exception\JsonFileDecodeException($msg);

        }catch (\Exception $e){

        }
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

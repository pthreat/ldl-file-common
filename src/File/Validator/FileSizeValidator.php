<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileSizeValidator implements ValidatorInterface
{
    /**
     * @var Config\FileSizeValidatorConfig
     */
    private $config;

    public function __construct(
        int $bytes,
        string $operator,
        bool $negated=false,
        bool $dumpable=true
    )
    {
        $this->config = new Config\FileSizeValidatorConfig($bytes, $operator, $negated, $dumpable);
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
        $size = filesize($path);

        switch($this->config->getOperator()){

            case Config\FileSizeValidatorConfig::OPERATOR_EQ:
                if($size === $this->config->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_GT:
                if($size > $this->config->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_GTE:
                if($size >= $this->config->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_LT:
                if($size < $this->config->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_LTE:
                if($size <= $this->config->getBytes()){
                    return;
                }
        }

        throw new \LogicException(
            sprintf(
                'File size of: "%s" (size: %s), is not "%s" than %s bytes',
                $path,
                $size,
                $this->config->getOperator(),
                $this->config->getBytes()
            )
        );
    }

    public function assertFalse($path): void
    {
        $size = filesize($path);

        switch($this->config->getOperator()){

            case Config\FileSizeValidatorConfig::OPERATOR_EQ:
                if($size !== $this->config->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_GT:
                if($size < $this->config->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_GTE:
                if($size <= $this->config->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_LT:
                if($size > $this->config->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_LTE:
                if($size >= $this->config->getBytes()){
                    return;
                }
        }

        throw new \LogicException(
            sprintf(
                'File size of: "%s" (size: %s), is "%s" than %s bytes',
                $path,
                $size,
                $this->config->getOperator(),
                $this->config->getBytes()
            )
        );
    }

    /**
     * @param ValidatorConfigInterface $config
     * @return ValidatorInterface
     * @throws \InvalidArgumentException
     */
    public static function fromConfig(ValidatorConfigInterface $config): ValidatorInterface
    {
        if(false === $config instanceof Config\HasRegexContentValidatorConfig){
            $msg = sprintf(
                'Config expected to be %s, config of class %s was given',
                __CLASS__,
                get_class($config)
            );
            throw new \InvalidArgumentException($msg);
        }

        /**
         * @var Config\FileSizeValidatorConfig $config
         */
        return new self(
            $config->getBytes(),
            $config->getOperator(),
            $config->isStrict()
        );
    }

    /**
     * @return Config\FileSizeValidatorConfig
     */
    public function getConfig(): Config\FileSizeValidatorConfig
    {
        return $this->config;
    }
}
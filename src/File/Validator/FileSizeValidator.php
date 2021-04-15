<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\HasValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileSizeValidator implements ValidatorInterface, HasValidatorConfigInterface
{
    /**
     * @var Config\FileSizeValidatorConfig
     */
    private $config;

    public function __construct(
        int $bytes,
        string $operator,
        bool $strict = true
    )
    {
        $this->config = new Config\FileSizeValidatorConfig($bytes, $operator, $strict);
    }

    /**
     * @param string $path
     * @param null $key
     * @param CollectionInterface|null $collection
     * @throws \LogicException
     */
    public function validate($path, $key = null, CollectionInterface $collection = null): void
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
<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorHasConfigInterfaceTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileSizeValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use ValidatorHasConfigInterfaceTrait;
    use NegatedValidatorTrait;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(
        int $bytes,
        string $operator,
        bool $negated=false,
        string $description=null
    )
    {
        $this->_tConfig = new Config\FileSizeValidatorConfig($bytes, $operator);
        $this->_tNegated = $negated;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File size must be %s than %s bytes',
                $this->_tConfig->getOperator(),
                $this->_tConfig->getBytes()
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $size = filesize($path);

        switch($this->_tConfig->getOperator()){

            case Config\FileSizeValidatorConfig::OPERATOR_EQ:
                if($size === $this->_tConfig->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_GT:
                if($size > $this->_tConfig->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_GTE:
                if($size >= $this->_tConfig->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_LT:
                if($size < $this->_tConfig->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_LTE:
                if($size <= $this->_tConfig->getBytes()){
                    return;
                }
        }

        throw new \LogicException(
            sprintf(
                'File size of: "%s" (size: %s), is not "%s" than %s bytes',
                $path,
                $size,
                $this->_tConfig->getOperator(),
                $this->_tConfig->getBytes()
            )
        );
    }

    public function assertFalse($path): void
    {
        $size = filesize($path);

        switch($this->_tConfig->getOperator()){

            case Config\FileSizeValidatorConfig::OPERATOR_EQ:
                if($size !== $this->_tConfig->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_GT:
                if($size < $this->_tConfig->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_GTE:
                if($size <= $this->_tConfig->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_LT:
                if($size > $this->_tConfig->getBytes()){
                    return;
                }
            case Config\FileSizeValidatorConfig::OPERATOR_LTE:
                if($size >= $this->_tConfig->getBytes()){
                    return;
                }
        }

        throw new \LogicException(
            sprintf(
                'File size of: "%s" (size: %s), is "%s" than %s bytes',
                $path,
                $size,
                $this->_tConfig->getOperator(),
                $this->_tConfig->getBytes()
            )
        );
    }

    /**
     * @param ValidatorConfigInterface $config
     * @param bool $negated
     * @param string|null $description
     * @return ValidatorInterface
     * @throws \InvalidArgumentException
     */
    public static function fromConfig(ValidatorConfigInterface $config, bool $negated = false, string $description=null): ValidatorInterface
    {
        if(false === $config instanceof Config\FileSizeValidatorConfig){
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
            $negated,
            $description
        );
    }
}
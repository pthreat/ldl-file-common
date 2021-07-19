<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorDescriptionTrait;
use LDL\Validators\Traits\ValidatorHasConfigInterfaceTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class MimeTypeValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use ValidatorHasConfigInterfaceTrait;
    use NegatedValidatorTrait;
    use ValidatorDescriptionTrait;

    public function __construct($types, bool $negated=false, string $description=null)
    {
        $this->_tConfig = new Config\MimeTypeValidatorConfig($types);
        $this->_tNegated = $negated;
        $this->_tDescription = $description ?? self::DESCRIPTION;
    }

    public function assertTrue($path): void
    {
        $mimeType = mime_content_type($path);

        if($this->_tConfig->getTypes()->hasValue($mimeType)){
            return;
        }

        throw new \LogicException(
            sprintf(
                '"%s" does not match given mime types: %s',
                $path,
                $this->_tConfig->getTypes()->implode(', ')
            )
        );
    }

    public function assertFalse($path): void
    {
        $mimeType = mime_content_type($path);

        if(!$this->_tConfig->getTypes()->hasValue($mimeType)){
            return;
        }

        $msg = sprintf(
            '"%s" matches mime types: "%s"',
            $path,
            $this->_tConfig->gettypes()->implode(', ')
        );

        throw new \LogicException($msg);
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
        if(false === $config instanceof Config\MimeTypeValidatorConfig){
            $msg = sprintf(
                'Config expected to be %s, config of class %s was given',
                __CLASS__,
                get_class($config)
            );
            throw new \InvalidArgumentException($msg);
        }

        /**
         * @var Config\MimeTypeValidatorConfig $config
         */
        return new self(
            $config->getTypes(),
            $negated,
            $description
        );
    }
}
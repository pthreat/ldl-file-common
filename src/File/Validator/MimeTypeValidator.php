<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\HasValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class MimeTypeValidator implements ValidatorInterface, HasValidatorConfigInterface
{
    /**
     * @var Config\MimeTypeValidatorConfig
     */
    private $config;

    public function __construct($types, bool $strict = true)
    {
        $this->config = new Config\MimeTypeValidatorConfig($types, $strict);
    }

    /**
     * @param string $path
     * @param null $key
     * @param CollectionInterface|null $collection
     * @throws \LogicException
     */
    public function validate($path, $key = null, CollectionInterface $collection = null): void
    {
        $mimeType = mime_content_type($path);

        if($this->config->isMatch()){
            if($this->config->getTypes()->hasValue($mimeType)){
                return;
            }

            throw new \LogicException(
                sprintf(
                    '"%s" does not match given mime types: %s',
                    $path,
                    $this->config->getTypes()->implode(', ')
                )
            );
        }

        if(!$this->config->getTypes()->hasValue($mimeType)){
            return;
        }

        $msg = sprintf(
            '"%s" matches mime types: "%s"',
            $path,
            $this->config->gettypes()->implode(', ')
        );

        throw new \LogicException($msg);
    }

    /**
     * @param ValidatorConfigInterface $config
     * @return ValidatorInterface
     * @throws \InvalidArgumentException
     */
    public static function fromConfig(ValidatorConfigInterface $config): ValidatorInterface
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
        return new self($config->getTypes(), $config->isStrict());
    }

    /**
     * @return Config\MimeTypeValidatorConfig
     */
    public function getConfig(): Config\MimeTypeValidatorConfig
    {
        return $this->config;
    }
}
<?php declare(strict_types=1);

namespace LDL\FS\File\Collection\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\FS\Util\FileValidatorHelper;
use LDL\Validators\Config\BasicValidatorConfig;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\HasValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileExistsValidator implements ValidatorInterface, HasValidatorConfigInterface
{
    /**
     * @var BasicValidatorConfig
     */
    private $config;

    public function __construct(bool $strict = true)
    {
        $this->config = new BasicValidatorConfig($strict);
    }

    /**
     * @param string $item
     * @param null $key
     * @param CollectionInterface|null $collection
     * @throws Exception\FileNotFoundException
     */
    public function validate($item, $key = null, CollectionInterface $collection = null): void
    {
        $path = FileValidatorHelper::getFilename($item);

        if(file_exists($path)){
            return;
        }

        $msg = "File \"$path\" does not exists";
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
        return new self($config->isStrict());
    }

    /**
     * @return BasicValidatorConfig
     */
    public function getConfig(): BasicValidatorConfig
    {
        return $this->config;
    }
}

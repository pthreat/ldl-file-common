<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorHasConfigInterfaceTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileTypeValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use ValidatorHasConfigInterfaceTrait;
    use NegatedValidatorTrait;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(iterable $types, bool $negated=false, string $description=null)
    {
        $this->_tConfig = new Config\FileTypeValidatorConfig($types);
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
                'File type must be one of: %s',
                implode(",", $this->_tConfig->getTypes()->toArray())
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $type = $this->initialValidation($path);

        if($this->_tConfig->getTypes()->hasValue($type)){
            return;
        }

        throw new \InvalidArgumentException('File type criteria not satisfied');
    }

    public function assertFalse($path): void
    {
        $type = $this->initialValidation($path);

        if(!$this->_tConfig->getTypes()->hasValue($type)){
            return;
        }

        throw new \InvalidArgumentException('File type criteria not satisfied');
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
        if(false === $config instanceof Config\FileTypeValidatorConfig){
            $msg = sprintf(
                'Config expected to be %s, config of class %s was given',
                __CLASS__,
                get_class($config)
            );
            throw new \InvalidArgumentException($msg);
        }

        /**
         * @var Config\FileTypeValidatorConfig $config
         */
        return new self(
            $config->getTypes(),
            $negated,
            $description
        );
    }

    private function initialValidation($path): string
    {
        $perms = fileperms($path);

        if(!$perms){
            throw new \InvalidArgumentException('Invalid file provided');
        }

        switch ($perms & 0xF000) {
            case 0xC000: // socket
                $type = Config\FileTypeValidatorConfig::FILE_TYPE_SOCKET;
                break;
            case 0xA000: // symbolic link
                $type = Config\FileTypeValidatorConfig::FILE_TYPE_LINK;
                break;
            case 0x8000: // regular
                $type = Config\FileTypeValidatorConfig::FILE_TYPE_REGULAR;
                break;
            case 0x6000: // block special
                $type = Config\FileTypeValidatorConfig::FILE_TYPE_BLOCK;
                break;
            case 0x4000: // directory
                $type = Config\FileTypeValidatorConfig::FILE_TYPE_DIRECTORY;
                break;
            case 0x2000: // character special
                $type = Config\FileTypeValidatorConfig::FILE_TYPE_CHAR;
                break;
            case 0x1000: // FIFO pipe
                $type = Config\FileTypeValidatorConfig::FILE_TYPE_FIFO;
                break;
            default: // unknown
                $type = Config\FileTypeValidatorConfig::FILE_TYPE_UNKNOWN;
        }

        return $type;
    }
}
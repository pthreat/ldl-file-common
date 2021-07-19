<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\HasValidatorResultInterface;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\ResetValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorHasConfigInterfaceTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class HasRegexContentValidator implements ValidatorInterface, NegatedValidatorInterface, HasValidatorResultInterface, ResetValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait {validate as _validate;}
    use ValidatorHasConfigInterfaceTrait;
    use NegatedValidatorTrait;

    /**
     * @var array
     */
    private $lines;

    /**
     * @var string|null
     */
    private $description;

    /**
     *
     * The match parameter specified if the regex should be matched or not, this is useful when you want to find
     * files which DO NOT HAVE a certain string. If you set match to true, then only files which comply to the
     * regex will be shown.
     *
     * @param string $regex
     * @param bool $storeLine
     * @param bool $negated
     * @param string|null $description
     */
    public function __construct(
        string $regex,
        bool $storeLine = true,
        bool $negated=false,
        string $description=null
    )
    {
        $this->_tConfig = new Config\HasRegexContentValidatorConfig(
            $regex,
            $storeLine
        );
        $this->_tNegated = $negated;
        $this->description = $description;
    }

    public function reset()
    {
        $this->lines = null;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File content must match with regex: %s',
                $this->_tConfig->getRegex()
            );
        }

        return $this->description;
    }

    /**
     * @param mixed $path
     * @throws \RuntimeException
     */
    public function validate($path): void
    {
        if(!is_readable($path)){
            $msg = "Could not open file \"$path\" in rb mode!\n";
            throw new \RuntimeException($msg);
        }

        $this->_validate($path);
    }

    public function assertTrue($path): void
    {
        $fp = @fopen($path, 'rb');
        $lineNo = 0;
        $hasMatches = false;

        while($line = fgets($fp)){
            $lineNo++;

            if(preg_match($this->_tConfig->getRegex(), $line)){
                $hasMatches = true;
                $this->lines[] = true === $this->_tConfig->isStoreLine() ? ['number' => $lineNo, 'line' => $line] : ['number' => $lineNo];
            }
        }

        fclose($fp);

        if($hasMatches){
            return;
        }

        throw new \LogicException("File: \"$path\" does not match criteria");
    }

    public function assertFalse($path): void
    {
        $fp = @fopen($path, 'rb');
        $lineNo = 0;
        $hasMatches = false;

        while($line = fgets($fp)){
            $lineNo++;

            if(preg_match($this->_tConfig->getRegex(), $line)){
                $hasMatches = true;
                $this->lines[] = true === $this->_tConfig->isStoreLine() ? ['number' => $lineNo, 'line' => $line] : ['number' => $lineNo];
            }
        }

        fclose($fp);

        if(!$hasMatches){
            return;
        }

        throw new \LogicException("File: \"$path\" match criteria");
    }

    public function getResult()
    {
        return $this->lines;
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
        if(false === $config instanceof Config\HasRegexContentValidatorConfig){
            $msg = sprintf(
                'Config expected to be %s, config of class %s was given',
                __CLASS__,
                get_class($config)
            );
            throw new \InvalidArgumentException($msg);
        }

        /**
         * @var Config\HasRegexContentValidatorConfig $config
         */
        return new self(
            $config->getRegex(),
            $config->isStoreLine(),
            $negated,
            $description
        );
    }
}
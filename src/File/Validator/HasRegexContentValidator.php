<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Validators\HasValidatorResultInterface;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class HasRegexContentValidator implements ValidatorInterface, HasValidatorResultInterface
{
    /**
     * @var Config\HasRegexContentValidatorConfig
     */
    private $config;

    /**
     * @var array
     */
    private $lines;

    /**
     *
     * The match parameter specified if the regex should be matched or not, this is useful when you want to find
     * files which DO NOT HAVE a certain string. If you set match to true, then only files which comply to the
     * regex will be shown.
     *
     * @param string $regex
     * @param bool $storeLine
     * @param bool $negated
     * @param bool $dumpable
     * @param string|null $description
     */
    public function __construct(
        string $regex,
        bool $storeLine = true,
        bool $negated=false,
        bool $dumpable=true,
        string $description=null
    )
    {
        $this->config = new Config\HasRegexContentValidatorConfig(
            $regex,
            $storeLine,
            $negated,
            $dumpable,
            $description
        );
    }

    /**
     * @param mixed $path
     * @throws \Exception
     */
    public function validate($path): void
    {
        if(!is_readable($path)){
            $msg = "File \"$path\" is not readable!\n";
            throw new \RuntimeException($msg);
        }

        $this->config->isNegated() ? $this->assertFalse($path) : $this->assertTrue($path);
    }

    public function assertTrue($path): void
    {
        $lineNo = 0;
        $hasMatches = false;

        $fp = @fopen($path, 'rb');

        while($line  = fgets($fp)){
            $lineNo++;

            if(preg_match($this->config->getRegex(), $line)){
                $hasMatches = true;
                $this->lines[] = true === $this->config->isStoreLine() ? ['number' => $lineNo, 'line' => $line] : ['number' => $lineNo];
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
        $lineNo = 0;
        $hasMatches = false;

        $fp = @fopen($path, 'rb');

        while($line  = fgets($fp)){
            $lineNo++;

            if(preg_match($this->config->getRegex(), $line)){
                $hasMatches = true;
                $this->lines[] = true === $this->config->isStoreLine() ? ['number' => $lineNo, 'line' => $line] : ['number' => $lineNo];
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
         * @var Config\HasRegexContentValidatorConfig $config
         */
        return new self(
            $config->getRegex(),
            $config->isStoreLine(),
            $config->isNegated(),
            $config->isDumpable(),
            $config->getDescription()
        );
    }

    /**
     * @return Config\HasRegexContentValidatorConfig
     */
    public function getConfig(): Config\HasRegexContentValidatorConfig
    {
        return $this->config;
    }
}
<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Validators\HasValidatorResultInterface;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\HasValidatorConfigInterface;
use LDL\Validators\ValidatorInterface;

class HasRegexContentValidator implements ValidatorInterface, HasValidatorConfigInterface, HasValidatorResultInterface
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
     * @param bool $match
     * @param bool $storeLine
     * @param bool $strict
     */
    public function __construct(
        string $regex,
        bool $match = true,
        bool $storeLine = true,
        bool $strict = true
    )
    {
        $this->config = new Config\HasRegexContentValidatorConfig($regex, $match, $storeLine, $strict);
    }

    /**
     * @param string $path
     * @param null $key
     * @param CollectionInterface|null $collection
     * @throws \LogicException
     */
    public function validate($path, $key = null, CollectionInterface $collection = null): void
    {
        $lineNo = 0;
        $hasMatches = false;

        $fp = @fopen($path, 'rb+');

        if(false === $fp){
            throw new \RuntimeException("File \"$path\" is not readable");
        }

        while($line  = fgets($fp)){
            $lineNo++;

            if(preg_match($this->config->getRegex(), $line)){
                $hasMatches = true;
                $this->lines[] = true === $this->config->isStoreLine() ? ['number' => $lineNo, 'line' => $line] : ['number' => $lineNo];
            }
        }

        fclose($fp);

        if($hasMatches && $this->config->isMatch()){
            return;
        }

        if(!$hasMatches && !$this->config->isMatch()){
            return;
        }

        throw new \LogicException("File: \"$path\" does not match criteria");
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
        return new self($config->getRegex(), $config->isStoreLine(), $config->isStrict());
    }

    /**
     * @return Config\HasRegexContentValidatorConfig
     */
    public function getConfig(): Config\HasRegexContentValidatorConfig
    {
        return $this->config;
    }
}
<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Helper\RegexHelper;
use LDL\Validators\HasValidatorResultInterface;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\ResetValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class HasRegexContentValidator implements ValidatorInterface, NegatedValidatorInterface, HasValidatorResultInterface, ResetValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait {validate as _validate;}
    use NegatedValidatorTrait;

    /**
     * @var string
     */
    private $regex;

    /**
     * @var bool
     */
    private $storeLine;

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
        RegexHelper::validate($regex);

        $this->regex = $regex;
        $this->storeLine = $storeLine;
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
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * @return bool
     */
    public function isStoreLine(): bool
    {
        return $this->storeLine;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File content must match with regex: %s',
                $this->regex
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

            if(preg_match($this->regex, $line)){
                $hasMatches = true;
                $this->lines[] = true === $this->storeLine ? ['number' => $lineNo, 'line' => $line] : ['number' => $lineNo];
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

            if(preg_match($this->regex, $line)){
                $hasMatches = true;
                $this->lines[] = true === $this->storeLine ? ['number' => $lineNo, 'line' => $line] : ['number' => $lineNo];
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

    public function jsonSerialize(): array
    {
        return $this->getConfig();
    }

    /**
     * @param array $data
     * @return ValidatorInterface
     * @throws Exception\FileValidatorException
     */
    public static function fromConfig(array $data = []): ValidatorInterface
    {
        if(false === array_key_exists('regex', $data)){
            $msg = sprintf("Missing property 'regex' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        try{
            return new self(
                (string) $data['regex'],
                array_key_exists('storeLine', $data) ? (bool) $data['storeLine'] : true,
                array_key_exists('negated', $data) ? (bool)$data['negated'] : false,
                $data['description'] ?? null
            );
        }catch(\Exception $e){
            throw new Exception\FileValidatorException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'regex' => $this->regex,
            'storeLine' => $this->storeLine,
            'negated' => $this->_tNegated,
            'description' => $this->getDescription()
        ];
    }
}
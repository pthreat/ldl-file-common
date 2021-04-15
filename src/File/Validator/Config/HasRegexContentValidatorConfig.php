<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Framework\Helper\RegexHelper;
use LDL\Validators\Config\ValidatorConfigInterface;
use LDL\Validators\Config\ValidatorConfigInterfaceTrait;

class HasRegexContentValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigInterfaceTrait;

    /**
     * @var string
     */
    private $regex;

    /**
     * @var bool
     */
    private $storeLine;

    /**
     * @var bool
     */
    private $match;

    public function __construct(string $regex, bool $match = true, bool $storeLine = true, bool $strict = true)
    {
        RegexHelper::validate($regex);

        $this->regex = $regex;
        $this->match = $match;
        $this->storeLine = $storeLine;
        $this->_isStrict = $strict;
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
    public function isMatch(): bool
    {
        return $this->match;
    }

    /**
     * @return bool
     */
    public function isStoreLine(): bool
    {
        return $this->storeLine;
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * @param array $data
     * @return ArrayFactoryInterface
     * @throws ArrayFactoryException
     */
    public static function fromArray(array $data = []): ArrayFactoryInterface
    {
        if(false === array_key_exists('regex', $data)){
            $msg = sprintf("Missing property 'regex' in %s", __CLASS__);
            throw new ArrayFactoryException($msg);
        }

        $match = array_key_exists('match', $data) ? (bool) $data['match'] : true;
        $storeLine = array_key_exists('storeLine', $data) ? (bool) $data['storeLine'] : true;

        try{
            return new self(
                (string) $data['regex'],
                $match,
                $storeLine,
                array_key_exists('strict', $data) ? (bool)$data['strict'] : true);
        }catch(\Exception $e){
            throw new ArrayFactoryException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'regex' => $this->regex,
            'match' => $this->match,
            'storeLine' => $this->storeLine,
            'strict' => $this->_isStrict
        ];
    }
}
<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Framework\Helper\RegexHelper;
use LDL\Validators\Config\Traits\ValidatorConfigTrait;
use LDL\Validators\Config\ValidatorConfigInterface;

class HasRegexContentValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigTrait;

    /**
     * @var string
     */
    private $regex;

    /**
     * @var bool
     */
    private $storeLine;

    public function __construct(
        string $regex,
        bool $storeLine = true
    )
    {
        RegexHelper::validate($regex);

        $this->regex = $regex;
        $this->storeLine = $storeLine;
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

        $storeLine = array_key_exists('storeLine', $data) ? (bool) $data['storeLine'] : true;

        try{
            return new self(
                (string) $data['regex'],
                $storeLine
            );
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
            'storeLine' => $this->storeLine
        ];
    }
}
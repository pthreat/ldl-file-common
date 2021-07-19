<?php declare(strict_types=1);

namespace LDL\File\Validator\Config;

use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Validators\Config\Traits\ValidatorConfigTrait;
use LDL\Validators\Config\ValidatorConfigInterface;

class FileSizeValidatorConfig implements ValidatorConfigInterface
{
    use ValidatorConfigTrait;

    public const OPERATOR_EQ='eq';
    public const OPERATOR_GT='gt';
    public const OPERATOR_GTE='gte';
    public const OPERATOR_LT='lt';
    public const OPERATOR_LTE='lte';

    /**
     * @var int
     */
    private $bytes;

    /**
     * @var string
     */
    private $operator;

    public function __construct(
        int $bytes,
        string $operator
    )
    {
        $operator = strtolower($operator);

        $validOperators = [
            self::OPERATOR_GT,
            self::OPERATOR_GTE,
            self::OPERATOR_LT,
            self::OPERATOR_LTE,
            self::OPERATOR_EQ,
        ];

        $isValidaOperator = in_array($operator, $validOperators, true);

        if(!$isValidaOperator){
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid comparison operator: %s, valid operators are: ',
                    implode(',', $validOperators)
                )
            );
        }

        $this->operator = $operator;
        $this->bytes = $bytes;
    }

    /**
     * @return int
     */
    public function getBytes(): int
    {
        return $this->bytes;
    }

    /**
     * @return string
     */
    public function getOperator() : string
    {
        return $this->operator;
    }

    /**
     * @param array $data
     * @return ArrayFactoryInterface
     * @throws ArrayFactoryException
     */
    public static function fromArray(array $data = []): ArrayFactoryInterface
    {
        if(false === array_key_exists('bytes', $data)){
            $msg = sprintf("Missing property 'bytes' in %s", __CLASS__);
            throw new ArrayFactoryException($msg);
        }

        try{
            return new self(
                (int) $data['bytes'],
                (string) $data['operator']

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
            'bytes' => $this->bytes,
            'operator' => $this->operator
        ];
    }
}
<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Framework\Helper\ComparisonOperatorHelper;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class FileSizeValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;

    /**
     * @var int
     */
    private $bytes;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(
        int $bytes,
        string $operator,
        bool $negated=false,
        string $description=null
    )
    {
        ComparisonOperatorHelper::validate($operator);

        $this->operator = $operator;
        $this->bytes = $bytes;
        $this->_tNegated = $negated;
        $this->description = $description;
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
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File size must be %s than %s bytes',
                $this->operator,
                $this->bytes
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $size = filesize($path);

        switch($this->operator){

            case ComparisonOperatorHelper::OPERATOR_STR_EQ:
                if($size === $this->bytes){
                    return;
                }
            case ComparisonOperatorHelper::OPERATOR_STR_GT:
                if($size > $this->bytes){
                    return;
                }
            case ComparisonOperatorHelper::OPERATOR_STR_GTE:
                if($size >= $this->bytes){
                    return;
                }
            case ComparisonOperatorHelper::OPERATOR_STR_LT:
                if($size < $this->bytes){
                    return;
                }
            case ComparisonOperatorHelper::OPERATOR_STR_LTE:
                if($size <= $this->bytes){
                    return;
                }
        }

        throw new \LogicException(
            sprintf(
                'File size of: "%s" (size: %s), is not "%s" than %s bytes',
                $path,
                $size,
                $this->operator,
                $this->bytes
            )
        );
    }

    public function assertFalse($path): void
    {
        $size = filesize($path);

        switch($this->operator){

            case ComparisonOperatorHelper::OPERATOR_STR_EQ:
                if($size !== $this->bytes){
                    return;
                }
            case ComparisonOperatorHelper::OPERATOR_STR_GT:
                if($size < $this->bytes){
                    return;
                }
            case ComparisonOperatorHelper::OPERATOR_STR_GTE:
                if($size <= $this->bytes){
                    return;
                }
            case ComparisonOperatorHelper::OPERATOR_STR_LT:
                if($size > $this->bytes){
                    return;
                }
            case ComparisonOperatorHelper::OPERATOR_STR_LTE:
                if($size >= $this->bytes){
                    return;
                }
        }

        throw new \LogicException(
            sprintf(
                'File size of: "%s" (size: %s), is "%s" than %s bytes',
                $path,
                $size,
                $this->operator,
                $this->bytes
            )
        );
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
        if(false === array_key_exists('bytes', $data)){
            $msg = sprintf("Missing property 'bytes' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        if(!array_key_exists('operator', $data)){
            $msg = sprintf("Missing property 'operator' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        if(!is_string($data['operator'])){
            throw new \InvalidArgumentException(
                sprintf('operator must be of type string, "%s" was given',gettype($data['operator']))
            );
        }

        try{
            return new self(
                (int) $data['bytes'],
                $data['operator'],
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
            'bytes' => $this->bytes,
            'operator' => $this->operator,
            'negated' => $this->_tNegated,
            'description' => $this->getDescription()
        ];
    }
}
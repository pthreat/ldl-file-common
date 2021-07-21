<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\Type\Collection\Types\String\StringCollection;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class MimeTypeValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;

    /**
     * @var StringCollection
     */
    private $types;

    /**
     * @var string|null
     */
    private $description;

    public function __construct($types, bool $negated=false, string $description=null)
    {
        if(count($types) === 0){
            throw new \InvalidArgumentException('The collection must have at least one mime type');
        }

        $this->types = new StringCollection($types);
        $this->_tNegated = $negated;
        $this->description = $description;
    }

    /**
     * @return StringCollection
     */
    public function getTypes(): StringCollection
    {
        return $this->types;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'Mime type must be one of the following: %s',
                $this->types->implode(', ')
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        $mimeType = mime_content_type($path);

        if($this->types->hasValue($mimeType)){
            return;
        }

        throw new \LogicException(
            sprintf(
                '"%s" does not match given mime types: %s',
                $path,
                $this->types->implode(', ')
            )
        );
    }

    public function assertFalse($path): void
    {
        $mimeType = mime_content_type($path);

        if(!$this->types->hasValue($mimeType)){
            return;
        }

        $msg = sprintf(
            '"%s" matches mime types: "%s"',
            $path,
            $this->types->implode(', ')
        );

        throw new \LogicException($msg);
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
        if(false === array_key_exists('types', $data)){
            $msg = sprintf("Missing property 'types' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        try{
            return new self(
                $data['types'],
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
            'types' => $this->types->toArray(),
            'negated' => $this->_tNegated,
            'description' => $this->getDescription()
        ];
    }
}
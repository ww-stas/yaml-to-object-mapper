<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\attributes;

use Attribute;
use Diezz\YamlToObjectMapper\CustomValidator;
use RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Validator
{
    private string $validatorClassName;

    public function __construct(string $validatorClassName)
    {
        $interfaces = class_implements($validatorClassName);
        if ($interfaces && !in_array(CustomValidator::class, $interfaces, true)) {
            throw new RuntimeException("Custom validator must implement 'CustomValidator'");
        }

        $this->validatorClassName = $validatorClassName;
    }

    public function getValidatorClassName(): string
    {
        return $this->validatorClassName;
    }
}

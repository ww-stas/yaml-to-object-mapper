<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Attributes;

use Attribute;

/**
 * Defines the setter name for non-public field
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Setter
{
    public function __construct(private string $setterName)
    {
    }

    public function getSetterName(): string
    {
        return $this->setterName;
    }
}

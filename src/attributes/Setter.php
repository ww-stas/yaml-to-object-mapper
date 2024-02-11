<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\attributes;

use Attribute;

/**
 * Defines the setter name for non-public field
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Setter
{
    private string $setterName;

    public function __construct(string $setterName)
    {
        $this->setterName = $setterName;
    }

    public function getSetterName(): string
    {
        return $this->setterName;
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Constructor
{
    public const DEFAULT_EMPTY = 'default.empty';
    public const STATIC_MAKE = 'static.make';

    private string $value;

    public function __construct(string $value = self::DEFAULT_EMPTY)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ResolverType
{
    public const  LAZY = 1;
    public const EAGER = 2;

    private int $type;

    public function __construct(int $type = self::EAGER)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
}

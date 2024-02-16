<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DefaultValueResolver
{
    public const PARENT_KEY = 'parent.key';
    public const NESTED_LIST = 'nested.list';

    private string $resolver;

    public function __construct(string $resolver = self::PARENT_KEY)
    {
        $this->resolver = $resolver;
    }

    /**
     * @return string
     */
    public function getResolver(): string
    {
        return $this->resolver;
    }
}

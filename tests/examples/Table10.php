<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\DefaultValueResolver;

class Table10
{
    #[DefaultValueResolver(resolver: DefaultValueResolver::PARENT_KEY)]
    public string $name;
    #[DefaultValueResolver(resolver: DefaultValueResolver::NESTED_LIST)]
    public array $columns;
}

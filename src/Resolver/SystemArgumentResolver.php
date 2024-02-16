<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

abstract class SystemArgumentResolver extends ArgumentResolver
{
    abstract public function findByPath(string $path): ?ArgumentResolver;
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

abstract class CustomArgumentResolver extends ArgumentResolver
{
    /**
     * The name of the resolver.
     *
     * @return string
     */
    abstract public function getName(): string;
}

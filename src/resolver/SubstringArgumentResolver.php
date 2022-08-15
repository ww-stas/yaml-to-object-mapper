<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class SubstringArgumentResolver extends ArgumentResolver
{
    protected function doResolve($context = null): string
    {
        return substr($context, (int)$this->method);
    }
}

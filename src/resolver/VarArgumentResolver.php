<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class VarArgumentResolver extends ArgumentResolver
{
    protected function doResolve($context = null)
    {
        return VarArgumentResolverConfig::make()->findAndResolve($this->method, $this->argument);
    }
}

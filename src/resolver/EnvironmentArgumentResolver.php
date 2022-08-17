<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class EnvironmentArgumentResolver extends ArgumentResolver
{
    protected function doResolve($context = null)
    {
        return getenv($this->method);
    }

    public function getName(): string
    {
        return 'env';
    }
}

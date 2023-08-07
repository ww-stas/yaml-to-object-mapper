<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class EnvironmentArgumentResolver extends CustomArgumentResolver
{
    protected function doResolve($context = null)
    {
        return getenv($this->rawValue);
    }

    public function getName(): string
    {
        return 'env';
    }
}

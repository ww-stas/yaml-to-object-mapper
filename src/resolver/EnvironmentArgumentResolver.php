<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class EnvironmentArgumentResolver extends CustomArgumentResolver
{
    protected function doResolve($context = null)
    {
        $variableName = $this->rawValue instanceof ArgumentResolver
            ? $this->rawValue->resolve($context)
            : $this->rawValue;

        return getenv($variableName);
    }

    public function getName(): string
    {
        return 'env';
    }
}

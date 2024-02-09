<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class EnvironmentArgumentResolver extends CustomArgumentResolver
{
    private ScalarArgumentResolver $envVariableName;

    /**
     * @param ScalarArgumentResolver $envVariableName
     */
    public function __construct(ScalarArgumentResolver $envVariableName)
    {
        $this->envVariableName = $envVariableName;
    }

    protected function doResolve($context = null): bool|array|string
    {
        return getenv($this->envVariableName->resolve($context));
    }
}

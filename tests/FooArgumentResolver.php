<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\Resolver\ArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\CustomArgumentResolver;

class FooArgumentResolver extends CustomArgumentResolver
{
    private ArgumentResolver $argument;

    /**
     * @param ArgumentResolver $argument
     */
    public function __construct(ArgumentResolver $argument)
    {
        $this->argument = $argument;
    }

    protected function doResolve($context = null): string
    {
        return 'foo' . $this->argument->resolve($context);
    }
}

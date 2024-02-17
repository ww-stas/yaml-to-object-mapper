<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

abstract class ArgumentResolver
{
    protected bool $isResolved = false;
    protected mixed $resolvedValue = null;
    protected ?ArgumentResolver $after = null;

    public function resolve($context = null): mixed
    {
        if ($this->isResolved) {
            return $this->resolvedValue;
        }

        $result = $this->doResolve($context);

        $this->resolvedValue = $result;
        $this->isResolved = true;

        return $result;
    }

    abstract protected function doResolve($context = null);
}

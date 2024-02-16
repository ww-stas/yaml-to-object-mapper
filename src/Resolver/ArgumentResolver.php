<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

abstract class ArgumentResolver
{
    protected bool $isResolved = false;
    protected mixed $resolvedValue = null;
    protected ?ArgumentResolver $after = null;

    /**
     * @param ArgumentResolver|null $after
     *
     * @return ArgumentResolver
     */
    public function setAfter(?ArgumentResolver $after): ArgumentResolver
    {
        $this->after = $after;

        return $this;
    }

    public function resolve($context = null): mixed
    {
        if ($this->isResolved) {
            return $this->resolvedValue;
        }

        $result = $this->doResolve($context);
        if ($this->after !== null) {
            $result = $this->after->resolve($result);
        }

        $this->resolvedValue = $result;
        $this->isResolved = true;

        return $result;
    }

    /**
     * @return ArgumentResolver|null
     */
    public function getAfter(): ?ArgumentResolver
    {
        return $this->after;
    }

    abstract protected function doResolve($context = null);
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

abstract class ArgumentResolver
{
    protected mixed $rawValue;
    protected mixed $argument;

    protected bool $isResolved = false;
    protected mixed $resolvedValue = null;
    protected ?ArgumentResolver $after = null;

    /**
     * @param string     $rawValue
     * @param mixed|null $argument
     */
    public function __construct(mixed $rawValue, mixed $argument = null)
    {
        $this->rawValue = $rawValue;
        $this->argument = $argument;
    }

    public function init(): void
    {
    }

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
     * @return string
     */
    public function getRawValue(): mixed
    {
        return $this->rawValue;
    }

    public function getArgument(): mixed
    {
        return $this->argument;
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

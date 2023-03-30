<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

abstract class ArgumentResolver
{
    protected mixed $method;
    protected mixed $argument;
    protected ?ArgumentResolver $after = null;

    /**
     * @param string     $method
     * @param mixed|null $argument
     */
    public function __construct(mixed $method, mixed $argument = null)
    {
        $this->method = $method;
        $this->argument = $argument;
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
        $result = $this->doResolve($context);
        if ($this->after !== null) {
            return $this->after->resolve($result);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getMethod(): mixed
    {
        return $this->method;
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

    /**
     * The name of the resolver.
     *
     * @return string
     */
    abstract public function getName(): string;
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

abstract class ArgumentResolver
{
    public const ARRAY_NOTATION = '/\[((.+),?)+]/';

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

//    public static function make(mixed $value): static
//    {
//        if (is_bool($value) || is_int($value)) {
//            return new ScalarArgumentResolver($value);
//        }
//
//        if (preg_match('/\|/', $value)) {
//            $values = explode("|", $value);
//            $resolvers = [];
//            foreach ($values as $val) {
//                $resolvers[] = static::make($val);
//            }
//            for ($i = count($resolvers) - 1; $i > 0; $i--) {
//                $resolvers[$i - 1]->setAfter($resolvers[$i]);
//            }
//
//            return $resolvers[0];
//        }
//
//        if (!preg_match('/\$?\w+::.+(::.+)?/', $value)) {
//            return new ScalarArgumentResolver($value);
//        }
//
//        $result = explode("::", $value);
//        if (count($result) === 3) {
//            [$provider, $method, $argument] = $result;
//        } else if (count($result) > 3) {
//            $provider = array_shift($result);
//            $method = array_shift($result);
//            $argument = $result;
//        } else {
//            [$provider, $method] = $result;
//            $argument = null;
//        }
//
//        if (preg_match('/\$([a-z]+)/i', $provider, $matches)) {
//            $provider = $matches[1];
//        }
//        if (is_string($argument) && preg_match(self::ARRAY_NOTATION, $argument, $matches)) {
//            $argument = [array_map(static fn($item) => trim($item), explode(',', $matches[1]))];
//        }
//        if (is_string($method) && preg_match(self::ARRAY_NOTATION, $method, $matches)) {
//            $method = array_map(static fn($item) => trim($item), explode(',', $matches[1]));
//        }
//
//        $providerClass = 'Diezz\YamlToObjectMapper\Resolver\\' . ucfirst($provider) . "ArgumentResolver";
//        if (!class_exists($providerClass)) {
//            throw new \RuntimeException("Provider $providerClass doesn't exist");
//        }
//        if (!is_subclass_of($providerClass, __CLASS__)) {
//            throw new \RuntimeException("The provider $providerClass must extends Provider abstract class");
//        }
//
//        /** @var  ArgumentResolver $instance */
//        return new $providerClass($method, $argument);
//    }

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

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use phpDocumentor\Reflection\Types\ClassString;

class ArgumentResolverFactory
{
    public const ARRAY_NOTATION = '/\[((.+),?)+]/';

    private ?string $resolversNamespace = null;

    private array $registeredResolvers = [];

    public function addResolver(ClassString $resolver): void
    {
        $this->registeredResolvers[] = $resolver;
    }

    /**
     * @throws ArgumentResolverException
     */
    public function create(mixed $value): ArgumentResolver
    {
        if (is_bool($value) || is_int($value)) {
            return new ScalarArgumentResolver($value);
        }

        //Chained resolvers separated by | symbol
        if (preg_match('/\|/', $value)) {
            $values = explode("|", $value);
            $resolvers = [];
            foreach ($values as $val) {
                $resolvers[] = $this->create($val);
            }
            for ($i = count($resolvers) - 1; $i > 0; $i--) {
                $resolvers[$i - 1]->setAfter($resolvers[$i]);
            }

            return $resolvers[0];
        }

        if (!preg_match('/\$?\w+::.+(::.+)?/', $value)) {
            return new ScalarArgumentResolver($value);
        }

        $result = explode("::", $value);
        if (count($result) === 3) {
            [$provider, $method, $argument] = $result;
        } else if (count($result) > 3) {
            $provider = array_shift($result);
            $method = array_shift($result);
            $argument = $result;
        } else {
            [$provider, $method] = $result;
            $argument = null;
        }

        if (preg_match('/\$([a-z]+)/i', $provider, $matches)) {
            $provider = $matches[1];
        }
        if (is_string($argument) && preg_match(self::ARRAY_NOTATION, $argument, $matches)) {
            $argument = [array_map(static fn($item) => trim($item), explode(',', $matches[1]))];
        }
        if (is_string($method) && preg_match(self::ARRAY_NOTATION, $method, $matches)) {
            $method = array_map(static fn($item) => trim($item), explode(',', $matches[1]));
        }

        $argumentResolverClassName = $this->findArgumentResolverClassName($provider);

        return new $argumentResolverClassName($method, $argument);
    }

    /**
     * @throws ArgumentResolverException
     *
     * @return ClassString
     */
    private function findArgumentResolverClassName(string $argumentResolverName): string {
        foreach ($this->registeredResolvers as $resolverCandidate) {
            $argumentResolverClassName = $resolverCandidate . ucfirst($argumentResolverName) . "ArgumentResolver";
            if (!class_exists($argumentResolverClassName)) {
                continue;
            }
            if (!is_subclass_of($argumentResolverClassName, ArgumentResolver::class)) {
                throw new ArgumentResolverException("The argument resolver $argumentResolverClassName must extends ArgumentResolver abstract class");
            }

            return $argumentResolverName;
        }

        throw new ArgumentResolverException("Couldn't find suitable argument resolver for name $argumentResolverName");
    }
}

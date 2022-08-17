<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use phpDocumentor\Reflection\Types\ClassString;
use RuntimeException;

class ArgumentResolverFactory
{
    public const ARRAY_NOTATION = '/\[((.+),?)+]/';

    private ?string $resolversNamespace = null;

    private array $registeredResolvers = [
        'format'    => FormatArgumentResolver::class,
        'substring' => SubstringArgumentResolver::class,
        'now'       => NowArgumentResolver::class,
        'self'      => SelfArgumentResolver::class,
    ];

    public function addResolver(ClassString $resolver): void
    {
        $instance = new $resolver;
        if (!$instance instanceof ArgumentResolver) {
            throw new RuntimeException('Resolver must be an instance of ArgumentResolver class');
        }
        $this->registeredResolvers[$instance->getName()] = $resolver;
    }

    /**
     * @throws ArgumentResolverException
     */
    public function create(mixed $value): ArgumentResolver
    {
        if (is_bool($value) || is_int($value) || is_array($value)) {
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
    private function findArgumentResolverClassName(string $argumentResolverName): string
    {
        if (!array_key_exists($argumentResolverName, $this->registeredResolvers)) {
            throw new ArgumentResolverException("Couldn't find suitable argument resolver for name $argumentResolverName");
        }

        $argumentResolverClassName = $this->registeredResolvers[$argumentResolverName];

        if (!class_exists($argumentResolverClassName)) {
            throw new ArgumentResolverException("Unable to find class $argumentResolverName");
        }
        if (!is_subclass_of($argumentResolverClassName, ArgumentResolver::class)) {
            throw new ArgumentResolverException("The argument resolver $argumentResolverClassName must extends ArgumentResolver abstract class");
        }

        return $argumentResolverClassName;
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use phpDocumentor\Reflection\Types\ClassString;
use RuntimeException;

class ArgumentResolverFactory
{
    private array $registeredResolvers = [
        'format'    => FormatArgumentResolver::class,
        'substring' => SubstringArgumentResolver::class,
        'now'       => NowArgumentResolver::class,
        'self'      => SelfArgumentResolver::class,
        'env'       => EnvironmentArgumentResolver::class,
    ];

    public function addResolver(string $resolverAlias, string $resolverClassName): void
    {
        $interfaces = class_implements($resolverClassName);
        if ($interfaces && in_array(CustomArgumentResolver::class, $interfaces, true)) {
            throw new RuntimeException('Resolver must be an instance of ArgumentResolver class');
        }
        if (array_key_exists($resolverAlias, $this->registeredResolvers)) {
            throw new RuntimeException("Resolver with name $resolverAlias has already registered");
        }
        $this->registeredResolvers[$resolverAlias] = $resolverClassName;
    }

    /**
     * @throws ArgumentResolverException
     */
    public function create(string $provider, array $arguments): ArgumentResolver
    {
        $argumentResolverClassName = $this->findArgumentResolverClassName($provider);
        if (empty($arguments)) {
            $arguments[] = null;
        }

        return new $argumentResolverClassName(...$arguments);
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

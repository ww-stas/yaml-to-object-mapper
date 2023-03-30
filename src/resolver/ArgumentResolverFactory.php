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
        'env'       => EnvironmentArgumentResolver::class,
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

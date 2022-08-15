<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use Diezz\YamlToObjectMapper\Resolver\Variables\CustomVariable;
use Diezz\YamlToObjectMapper\Resolver\Variables\NowCustomVar;

class VarArgumentResolverConfig
{
    private static ?VarArgumentResolverConfig $instance = null;
    private array $variables = [];

    private function __construct()
    {
    }

    public static function make(): static
    {
        if (self::$instance === null) {
            self::$instance = new static();
            self::$instance->register(new NowCustomVar());
        }

        return self::$instance;
    }

    public function register(CustomVariable $customVariable): void
    {
        $this->variables[$customVariable->getName()] = $customVariable;
    }

    public function registerCallback(string $name, callable $callback): void
    {
        $this->variables[$name] = $callback;
    }

    public function findAndResolve(string $name, string $argument): mixed
    {
        if (!array_key_exists($name, $this->variables)) {
            throw new \RuntimeException("Unregistered resolver for custom variable $name");
        }

        $resolver = $this->variables[$name];
        if ($resolver instanceof CustomVariable) {
            return $resolver->resolve($argument);
        }

        if (is_callable($resolver)) {
            return $resolver($argument);
        }

        throw new \RuntimeException('Unable to resolve custom variable');
    }
}

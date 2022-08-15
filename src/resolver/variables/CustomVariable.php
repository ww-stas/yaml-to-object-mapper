<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Variables;

use Diezz\YamlToObjectMapper\Resolver\ArgumentResolver;

/**
 * Custom variable is any variable you can define in your scope.
 * Each variable in a config file should start with `$` sign
 */
abstract class CustomVariable extends ArgumentResolver
{
    /**
     * The name of the variable.
     * @return string
     */
    abstract public function getName(): string;
}

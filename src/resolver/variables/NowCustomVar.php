<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Variables;

use DateTime;

/**
 * Returns current timestamp in given format;
 * If format isn't provided by default `Y-m-d H:i:s` would be used.
 *
 * Example of usage:
 * $now::Y-m-d H:i:s
 */
class NowCustomVar extends CustomVariable
{
    public function getName(): string
    {
        return 'now';
    }

    protected function doResolve($context = null): string
    {
        return (new DateTime())->format($this->argument);
    }
}

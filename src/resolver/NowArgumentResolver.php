<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use DateTime;

/**
 * Returns current timestamp in given format;
 * If format isn't provided by default `Y-m-d H:i:s` would be used.
 *
 * Example of usage:
 * $now::Y-m-d H:i:s
 */
class NowArgumentResolver extends CustomArgumentResolver
{
    public function getName(): string
    {
        return 'now';
    }

    protected function doResolve($context = null): mixed
    {
        $now = new DateTime();
        if ($this->rawValue !== null) {
            return $now->format($this->rawValue);
        }

        return $now;
    }
}

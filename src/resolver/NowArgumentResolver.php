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
    private ?ScalarArgumentResolver $format;

    /**
     * @param ScalarArgumentResolver|null $format
     */
    public function __construct(?ScalarArgumentResolver $format)
    {
        $this->format = $format;
    }

    protected function doResolve($context = null): mixed
    {
        $now = new DateTime();
        if ($this->format !== null) {
            return $now->format($this->format->resolve($context));
        }

        return $now;
    }
}

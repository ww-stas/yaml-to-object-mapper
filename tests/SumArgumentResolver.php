<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\Resolver\ArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\CustomArgumentResolver;

class SumArgumentResolver extends CustomArgumentResolver
{
    /**
     * @var ArgumentResolver[]
     */
    private array $arguments;

    /**
     * @param ArgumentResolver[] $arguments
     */
    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
    }

    protected function doResolve($context = null): int
    {
        $sum = 0;
        foreach ($this->arguments as $iValue) {
            $sum += $iValue->resolve($context);
        }

        return $sum;
    }
}

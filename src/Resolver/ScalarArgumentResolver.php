<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class ScalarArgumentResolver extends CustomArgumentResolver
{
    private mixed $scalar;

    /**
     * @param mixed $scalar
     */
    public function __construct(mixed $scalar)
    {
        $this->scalar = $scalar;
    }

    protected function doResolve($context = null)
    {
        return $this->scalar;
    }
}

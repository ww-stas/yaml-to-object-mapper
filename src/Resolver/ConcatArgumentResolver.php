<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class ConcatArgumentResolver extends CustomArgumentResolver
{
    /**
     * @var ArgumentResolver[]
     */
    private array $parts;

    /**
     * @param ArgumentResolver[] $parts
     */
    public function __construct(array $parts)
    {
        $this->parts = $parts;
    }

    protected function doResolve($context = null)
    {
        $output = "";
        foreach ($this->parts as $item) {
            $output .= $item->resolve($context);
        }

        return $output;
    }
}

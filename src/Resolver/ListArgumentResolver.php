<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class ListArgumentResolver extends SystemArgumentResolver
{
    /**
     * @var ArgumentResolver[]
     */
    private array $elements;

    /**
     * @param ArgumentResolver[] $elements
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    protected function doResolve($context = null): array
    {
        $result = [];
        foreach ($this->elements as $key => $item) {
            $result[$key] = $item->resolve($context);
        }

        return $result;
    }

    public function findByPath(string $path): ?ArgumentResolver
    {
        return $this->elements[$path] ?? null;
    }
}

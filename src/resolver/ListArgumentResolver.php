<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class ListArgumentResolver extends ArgumentResolver
{
    protected function doResolve($context = null)
    {
        $result = [];
        foreach ($this->rawValue as $key => $item) {
            $result[$key] = $item->resolve($context);
        }

        return $result;
    }

    public function getName(): string
    {
        return 'list';
    }
}

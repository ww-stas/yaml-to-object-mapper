<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class ConcatArgumentResolver extends CustomArgumentResolver
{
    protected function doResolve($context = null)
    {
        $output = "";
        foreach ($this->rawValue as $item) {
            $output .= $item->resolve($context);
        }

        return $output;
    }
}

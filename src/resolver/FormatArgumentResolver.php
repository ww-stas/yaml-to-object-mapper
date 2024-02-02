<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class FormatArgumentResolver extends CustomArgumentResolver
{
    protected function doResolve($context = null)
    {
        $value = $this->rawValue;
        if ($value instanceof ArgumentResolver) {
            $value = $value->resolve($context);
        }

        return $value->format($this->getFormat($context));
    }

    private function getFormat($context): string
    {
        return $this->argument->resolve($context);
    }

    public function getName(): string
    {
        return 'format';
    }
}

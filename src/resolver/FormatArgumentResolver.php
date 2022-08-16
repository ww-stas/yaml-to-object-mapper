<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class FormatArgumentResolver extends ArgumentResolver
{
    protected function doResolve($context = null)
    {
        if ($context === null) {
            throw new \RuntimeException('The context must not be null');
        }

        if (!$context instanceof \DateTime) {
            throw new \RuntimeException('The context must be the instance of \DateTime ');
        }

        return $context->format($this->method);
    }

    public function getName(): string
    {
        return 'format';
    }
}

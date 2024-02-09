<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class FormatArgumentResolver extends CustomArgumentResolver
{
    private ArgumentResolver $value;
    private ArgumentResolver $format;

    /**
     * @param ArgumentResolver $value
     * @param ArgumentResolver $format
     */
    public function __construct(ArgumentResolver $value, ArgumentResolver $format)
    {
        $this->value = $value;
        $this->format = $format;
    }


    protected function doResolve($context = null)
    {
        return $this->value->resolve($context)->format($this->getFormat($context));
    }

    private function getFormat($context): string
    {
        return $this->format->resolve($context);
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class FormatArgumentResolver extends CustomArgumentResolver
{
    private ArgumentResolver $date;
    private ArgumentResolver $format;

    /**
     * @param ArgumentResolver $date
     * @param ArgumentResolver $format
     */
    public function __construct(ArgumentResolver $date, ArgumentResolver $format)
    {
        $this->date = $date;
        $this->format = $format;
    }


    protected function doResolve($context = null)
    {
        return $this->date->resolve($context)->format($this->getFormat($context));
    }

    private function getFormat($context): string
    {
        return $this->format->resolve($context);
    }
}

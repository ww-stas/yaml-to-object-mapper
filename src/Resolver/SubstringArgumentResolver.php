<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class SubstringArgumentResolver extends CustomArgumentResolver
{
    private ArgumentResolver $string;
    private ArgumentResolver $offset;
    private ?ArgumentResolver $length;

    /**
     * @param ScalarArgumentResolver $string
     * @param ScalarArgumentResolver $offset
     */
    public function __construct(ArgumentResolver $string, ArgumentResolver $offset, ?ArgumentResolver $length = null)
    {
        $this->string = $string;
        $this->offset = $offset;
        $this->length = $length;
    }

    protected function doResolve($context = null): string
    {
        $string = (string)$this->string->resolve($context);
        $lengthOrOffset = (int)$this->offset->resolve($context);
        if ($this->length === null) {
            $offset = 0;
            $length = $lengthOrOffset;
        } else {
            $offset = $lengthOrOffset;
            $length = (int)$this->length->resolve($context);
        }

        return substr($string, $offset, $length);
    }
}

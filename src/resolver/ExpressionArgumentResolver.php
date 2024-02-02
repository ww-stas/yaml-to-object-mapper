<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use Diezz\YamlToObjectMapper\Resolver\Parser\Parser;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;

class ExpressionArgumentResolver extends CustomArgumentResolver
{
    /**
     * @throws SyntaxException
     */
    protected function doResolve($context = null)
    {
        return (new Parser($this->rawValue))->parse()->toResolver($context);
    }

    public function getName(): string
    {
        return "expression";
    }
}

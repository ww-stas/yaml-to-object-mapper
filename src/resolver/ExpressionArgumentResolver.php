<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use Diezz\YamlToObjectMapper\Resolver\Parser\Parser;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;

class ExpressionArgumentResolver extends ArgumentResolver
{
    /**
     * @throws SyntaxException
     */
    protected function doResolve($context = null)
    {
        return (new Parser($this->rawValue))->parse()->run($context);
    }

    public function getName(): string
    {
        return "expression";
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

use Diezz\YamlToObjectMapper\Resolver\ArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverFactory;
use Diezz\YamlToObjectMapper\Resolver\ScalarArgumentResolver;
use JetBrains\PhpStorm\ArrayShape;

class StringLiteral extends ASTNode
{
    public function __construct(
        private string $value
    )
    {
    }

    public function getType(): string
    {
        return 'StringLiteral';
    }

    public function getValue(): string
    {
        return $this->value;
    }

    #[ArrayShape(['type' => "string", 'value' => "string"])]
    public function toArray(): array
    {
        return [
            'type'  => $this->getType(),
            'value' => $this->value,
        ];
    }

    public function toResolver(ArgumentResolverFactory $argumentResolverFactory): ArgumentResolver
    {
        return new ScalarArgumentResolver($this->getValue());
    }
}

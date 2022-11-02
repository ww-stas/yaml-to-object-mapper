<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

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

    public function run(mixed $context): mixed
    {
        return $this->getValue();
    }
}

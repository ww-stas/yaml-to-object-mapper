<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

use JetBrains\PhpStorm\ArrayShape;

class ArrayExpression extends ASTNode
{
    /**
     * @param ASTNode[] $values
     */
    public function __construct(
        private array $values
    )
    {
    }

    public function getType(): string
    {
        return 'ArrayExpression';
    }

    /**
     * @return ASTNode[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    #[ArrayShape(['type' => "string", 'values' => "array"])]
    public function toArray(): array
    {
        $values = [];
        foreach ($this->getValues() as $value) {
            $values[] = $value->toArray();
        }

        return [
            'type'   => $this->getType(),
            'values' => $values,
        ];
    }

    public function run(mixed $context): mixed
    {
        $result = [];

        foreach ($this->values as $value) {
            $result[] = $value->run($context);
        }

        return $result;
    }
}

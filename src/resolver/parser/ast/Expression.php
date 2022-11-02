<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

use JetBrains\PhpStorm\ArrayShape;

class Expression extends ASTNode
{

    /**
     * @param ASTNode[] $body
     */
    public function __construct(
        private array $body
    )
    {
    }

    public function getType(): string
    {
        return 'Expression';
    }

    /**
     * @return ASTNode[]
     */
    public function getBody(): array
    {
        return $this->body;
    }

    #[ArrayShape(['type' => "string", 'body' => "array"])]
    public function toArray(): array
    {
        $body = [];
        foreach ($this->body as $value) {
            $body[] = $value->toArray();
        }

        return [
            'type' => $this->getType(),
            'body' => $body,
        ];
    }

    public function run(mixed $context): mixed
    {
        $result = [];

        foreach ($this->body as $node) {
            $result[] = $node->run($context);
        }

        //Just for now return a string
        return implode('', $result);
    }


}

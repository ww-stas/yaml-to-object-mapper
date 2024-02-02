<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

use Diezz\YamlToObjectMapper\Resolver\ArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\ConcatArgumentResolver;
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

    public function toResolver(): ArgumentResolver
    {
        if (count($this->body) === 1) {
            return $this->body[0]->toResolver();
        }

        $arguments = [];

        foreach ($this->body as $node) {
            $arguments[] = $node->toResolver();
        }

        //Just for now return a string
        //return implode('', $arguments);
        return new ConcatArgumentResolver($arguments);
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

use JetBrains\PhpStorm\ArrayShape;

class PathArgument extends ASTNode
{

    public function __construct(
        private array $path = []
    )
    {
    }

    public function getPath(): array
    {
        return $this->path;
    }

    public function addPathItem($item): void
    {
        $this->path[] = $item;
    }

    public function getType(): string
    {
        return 'PathArgument';
    }

    #[ArrayShape(['type' => "string", 'path' => "array"])]
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'path' => $this->getPath(),
        ];
    }

    public function run(mixed $context): array
    {
        return $this->getPath();
    }
}

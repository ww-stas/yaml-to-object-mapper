<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

abstract class ASTNode
{
    abstract public function getType(): string;

    //abstract public function getValue(): string;

    abstract public function toArray(): array;

    abstract public function run(mixed $context): mixed;
}

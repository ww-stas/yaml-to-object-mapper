<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

use Diezz\YamlToObjectMapper\Resolver\ArgumentResolver;

abstract class ASTNode
{
    abstract public function getType(): string;

    //abstract public function getValue(): string;

    abstract public function toArray(): array;

    abstract public function toResolver(): ArgumentResolver;
}

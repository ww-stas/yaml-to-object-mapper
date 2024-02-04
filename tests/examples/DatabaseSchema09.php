<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\Collection;

class DatabaseSchema09
{
    /**
     * @var Table09[]
     */
    #[Collection(class: Table09::class)]
    public array $tables;
}

<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\Collection;

class DatabaseSchema10
{
    /**
     * @var Table09[]
     */
    #[Collection(class: Table10::class)]
    public array $tables;
}

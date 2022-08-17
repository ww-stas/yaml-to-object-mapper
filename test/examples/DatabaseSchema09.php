<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\Collection;
use Diezz\YamlToObjectMapper\YamlConfigurable;

class DatabaseSchema09 implements YamlConfigurable
{
    /**
     * @var Table09[]
     */
    #[Collection(class: Table09::class)]
    public array $tables;
}

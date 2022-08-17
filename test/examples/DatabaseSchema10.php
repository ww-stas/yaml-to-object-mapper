<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\Collection;
use Diezz\YamlToObjectMapper\YamlConfigurable;

class DatabaseSchema10 implements YamlConfigurable
{
    /**
     * @var Table09[]
     */
    #[Collection(class: Table10::class)]
    public array $tables;
}

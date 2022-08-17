<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\YamlConfigurable;

class Table09 implements YamlConfigurable
{
    public string $name;
    public array $columns;
}

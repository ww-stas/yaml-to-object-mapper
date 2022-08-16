<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\YamlConfigurable;

class ListModel implements YamlConfigurable
{
    public string $name;
    public array $list;
}

<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\YamlConfigurable;

class Self03 implements YamlConfigurable
{
    public string $name;
    public string $target;
}
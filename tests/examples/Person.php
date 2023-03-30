<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\YamlConfigurable;

class Person implements YamlConfigurable
{
    public string $name;
    public int $age;
}

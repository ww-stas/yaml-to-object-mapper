<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\YamlConfigurable;

class EnvVariable11 implements YamlConfigurable
{
    public string $name;
    public string $target;
}

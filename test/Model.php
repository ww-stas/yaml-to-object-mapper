<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\YamlConfigurable;

class Model implements YamlConfigurable
{
    public string $name;
    public array $values;
}

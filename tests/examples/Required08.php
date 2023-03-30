<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\Required;
use Diezz\YamlToObjectMapper\YamlConfigurable;

class Required08 implements YamlConfigurable
{
    public string $name;

    #[Required]
    public string $target;
}

<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\Required;

class Required08
{
    public string $name;

    #[Required]
    public string $target;
}

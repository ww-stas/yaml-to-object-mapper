<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Examples;

use Diezz\YamlToObjectMapper\Attributes\Required;

class Required08
{
    public string $name;

    #[Required]
    public string $target;
}

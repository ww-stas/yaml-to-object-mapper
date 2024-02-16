<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Examples;

use Diezz\YamlToObjectMapper\Attributes\IgnoreUnknown;

#[IgnoreUnknown]
class ConcatExample17
{
    public string $name;
    public string $value;
}

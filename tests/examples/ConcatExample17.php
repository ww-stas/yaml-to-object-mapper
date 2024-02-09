<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\IgnoreUnknown;

#[IgnoreUnknown]
class ConcatExample17
{
    public string $name;
    public string $value;
}

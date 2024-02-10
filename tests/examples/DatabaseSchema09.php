<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Examples;

use Diezz\YamlToObjectMapper\Attributes\Collection;
use Diezz\YamlToObjectMapper\Attributes\IgnoreUnknown;

#[IgnoreUnknown]
class DatabaseSchema09
{
    /**
     * @var Table09[]
     */
    #[Collection(class: Table09::class)]
    public array $tables;
}

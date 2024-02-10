<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Examples;

use Diezz\YamlToObjectMapper\Attributes\Collection;
use Diezz\YamlToObjectMapper\Attributes\IgnoreUnknown;

#[IgnoreUnknown]
class DatabaseSchema10
{
    /**
     * @var Table09[]
     */
    #[Collection(class: Table10::class)]
    public array $tables;
}

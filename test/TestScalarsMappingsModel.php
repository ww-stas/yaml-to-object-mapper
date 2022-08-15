<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\Attributes\Collection;
use Diezz\YamlToObjectMapper\YamlConfigurable;

class TestScalarsMappingsModel implements YamlConfigurable
{
    public string $stringValue;
    /**
     * @var string[]
     */
    public array $list;
    public int $integerValue;
    public bool $booleanValue;
}

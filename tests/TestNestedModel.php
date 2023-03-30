<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\YamlConfigurable;

class TestNestedModel implements YamlConfigurable
{
    /**
     * @var string[]
     */
    public array $list;
    public int $integerValue;
    public bool $booleanValue;
    public Model $model;
}

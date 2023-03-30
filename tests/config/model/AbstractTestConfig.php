<?php declare(strict_types=1);

namespace Test\Config\Model;

use Diezz\YamlToObjectMapper\YamlConfigurable;
use phpDocumentor\Reflection\Types\Integer;

abstract class AbstractTestConfig
{
    abstract public function getConfigClass(): YamlConfigurable;
    abstract public function getYaml(): string;
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use Diezz\YamlToObjectMapper\ClassInfo;

class Context
{
    public function __construct(
        private array     $config,
        private ClassInfo $classInfo,
    )
    {
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return ClassInfo
     */
    public function getClassInfo(): ClassInfo
    {
        return $this->classInfo;
    }
}

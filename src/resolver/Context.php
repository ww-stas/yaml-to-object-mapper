<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class Context
{
    public function __construct(
        private array                  $config,
        private SystemArgumentResolver $mappingConfig,
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

    public function getMappingConfig(): SystemArgumentResolver
    {
        return $this->mappingConfig;
    }
}

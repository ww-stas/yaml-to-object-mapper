<?php declare(strict_types=1);

namespace Test\Config\Model\Example1;

use Diezz\YamlToObjectMapper\YamlConfigurable;

class Config implements YamlConfigurable
{
    private Nested $nested;
    private int $integer;

    /**
     * @return Nested
     */
    public function getNested(): Nested
    {
        return $this->nested;
    }

    /**
     * @param Nested $nested
     *
     * @return Config
     */
    public function setNested(Nested $nested): Config
    {
        $this->nested = $nested;

        return $this;
    }
}

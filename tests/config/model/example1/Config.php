<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Config\Model\Example1;

class Config
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

<?php declare(strict_types=1);

namespace Test\Config\Model\Example1;

use Diezz\YamlToObjectMapper\Attributes\Required;
use Diezz\YamlToObjectMapper\YamlConfigurable;

class Nested implements YamlConfigurable
{
    #[Required]
    private string $field;

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return Nested
     */
    public function setField(string $field): Nested
    {
        $this->field = $field;

        return $this;
    }
}

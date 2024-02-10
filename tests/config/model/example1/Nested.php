<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Config\Model\Example1;

use Diezz\YamlToObjectMapper\Attributes\Required;

class Nested
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

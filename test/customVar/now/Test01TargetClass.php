<?php declare(strict_types=1);

namespace Test\CustomVar\Now;

use Diezz\YamlToObjectMapper\Attributes\Required;
use Diezz\YamlToObjectMapper\Attributes\ResolverType;
use Diezz\YamlToObjectMapper\YamlConfigurable;

class Test01TargetClass implements YamlConfigurable
{
    #[Required]
    #[ResolverType(ResolverType::EAGER)]
    private string $value;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}

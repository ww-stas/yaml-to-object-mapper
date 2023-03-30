<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\Collection;
use Diezz\YamlToObjectMapper\YamlConfigurable;

class ListModel02 implements YamlConfigurable
{
    public string $name;

    /**
     * @var Person[]
     */
    #[Collection(class: Person::class)]
    public array $persons;
}

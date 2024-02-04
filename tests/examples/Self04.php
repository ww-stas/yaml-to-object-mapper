<?php declare(strict_types=1);

namespace Test\Examples;

use Diezz\YamlToObjectMapper\Attributes\Collection;

class Self04
{
    public string $name;

    /**
     * @var Person[]
     */
    #[Collection(class: Person::class)]
    public array $persons;

    public string $target;
}
<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Examples;

class ListModelPrivate01
{
    private string $name;
    private array $list;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): void
    {
        $this->list = $list;
    }
}

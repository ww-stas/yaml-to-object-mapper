<?php declare(strict_types=1);

namespace Test\Config\Model;

abstract class AbstractTestConfig
{
    abstract public function getConfigClass(): object;

    abstract public function getYaml(): string;
}

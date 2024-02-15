<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

interface CustomValidator
{
    public function validate($value): bool;
}

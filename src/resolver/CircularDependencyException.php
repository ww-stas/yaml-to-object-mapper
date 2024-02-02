<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use RuntimeException;

class CircularDependencyException extends RuntimeException
{
    public function __construct(array $path)
    {
        $message = 'Circular dependency detected at path: ';
        foreach ($path as $item) {
            $message .= "$item->";
        }
        parent::__construct(substr($message, 0, -2));
    }
}

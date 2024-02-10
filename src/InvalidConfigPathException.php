<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

use RuntimeException;

class InvalidConfigPathException extends RuntimeException
{
    public function __construct(array $config, ClassInfo $classInfo)
    {
        $message = "Yaml file contains properties that doesn't exists on a target class {$classInfo->getClassName()}:";
        foreach (array_keys($config) as $fieldName) {
            $message .= "\n- $fieldName";
        }
        $message .= "\nTo prevent this behaviour use #[IgnoreUnknown] on a target class";
        parent::__construct($message);
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use Diezz\YamlToObjectMapper\ClassField;
use Diezz\YamlToObjectMapper\ClassInfo;

class InstanceArgumentResolver extends SystemArgumentResolver
{
    private ClassInfo $classInfo;
    private array $config;

    /**
     * @param ClassInfo $classInfo
     * @param array     $config
     */
    public function __construct(ClassInfo $classInfo, array $config)
    {
        $this->classInfo = $classInfo;
        $this->config = $config;
    }

    protected function doResolve($context = null)
    {
        $className = $this->classInfo->getClassName();
        $instance = new $className;
        foreach ($this->config as $fieldName => $argumentResolver) {
            $classField = $this->classInfo->getClassField($fieldName);
            if ($classField === null) {
                continue;
            }
            $value = $argumentResolver->resolve($context);
            $this->setValue($classField, $value, $instance);
        }

        return $instance;
    }

    private function setValue(ClassField $field, $value, $resultInstance): void
    {
        if ($value === null && $field->hasDefaultValue()) {
            return;
        }

        if ($field->isPublic()) {
            $resultInstance->{$field->getName()} = $value;
        } else {
            $resultInstance->{$field->getSetter()}($value);
        }
    }

    public function findByPath(string $path): ?ArgumentResolver
    {
        return $this->config[$path] ?? null;
    }
}

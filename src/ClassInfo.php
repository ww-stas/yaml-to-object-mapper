<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

class ClassInfo
{
    /**
     * @var ClassField[]
     */
    private array $fields = [];
    private string $className;

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return ClassField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function addClassField(string $property, ClassField $classField): void
    {
        $this->fields[$property] = $classField;
    }

    public function getClassField(string $fieldName): ?ClassField
    {
        return $this->fields[$fieldName] ?? null;
    }
}

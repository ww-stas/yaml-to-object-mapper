<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

use Diezz\YamlToObjectMapper\Attributes\Collection;
use Diezz\YamlToObjectMapper\Attributes\DefaultValueResolver;
use Diezz\YamlToObjectMapper\Attributes\Required;
use Diezz\YamlToObjectMapper\Attributes\ResolverType;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class ClassInfoReflector
{
    /**
     * @throws ReflectionException
     */
    public function introspect(string $targetClass): ClassInfo
    {
        $instance = new ClassInfo();
        $instance->setClassName($targetClass);

        $reflection = new ReflectionClass($targetClass);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $classField = new ClassField();
            $propertyName = $property->getName();

            $classField->setName($propertyName);
            $classField->setRequired($this->isRequired($property));
            $classField->setHasDefaultValue($property->hasDefaultValue());
            $classField->setDefaultValueResolver($this->getDefaultValueResolver($property));
            $classField->setArgumentResolverType($this->getArgumentResolverType($property));
            $this->resolveType($property, $classField);
            $this->resolveSetter($reflection, $property, $classField);

            $instance->addClassField($propertyName, $classField);
        }

        $this->fixCircularReferences($instance);

        return $instance;
    }

    private function getDefaultValueResolver(ReflectionProperty $reflectionProperty): ?string
    {
        $attributes = $reflectionProperty->getAttributes(DefaultValueResolver::class);
        if (!empty($attributes)) {
            /** @var DefaultValueResolver $attribute */
            $attribute = $attributes[0]->newInstance();

            return $attribute->getResolver();
        }

        return null;
    }

    private function getArgumentResolverType(ReflectionProperty $reflectionProperty): int
    {
        $attributes = $reflectionProperty->getAttributes(ResolverType::class);
        if (!empty($attributes)) {
            /** @var ResolverType $attribute */
            $attribute = $attributes[0]->newInstance();

            return $attribute->getType();
        }

        return ResolverType::EAGER;
    }

    private function getNestedClassInfos(ClassInfo $classInfo): array
    {
        $map = [];

        foreach ($classInfo->getFields() as $field) {
            if ($field->getClassInfo() !== null) {
                $map[$field->getType()] = $field->getClassInfo();
                foreach ($this->getNestedClassInfos($field->getClassInfo()) as $k => $f) {
                    $map[$k] = $f;
                }
            }
        }

        return $map;
    }

    private function resolveSetter(ReflectionClass $reflectionClass, ReflectionProperty $reflectionProperty, ClassField $classField): void
    {
        $isPublic = $reflectionProperty->isPublic();
        $classField->setIsPublic($isPublic);

        if (true === $isPublic) {
            return;
        }

        $possibleSetter = "set" . ucfirst($reflectionProperty->getName());
        if (false === $reflectionClass->hasMethod($possibleSetter)) {
            throw new \RuntimeException(sprintf(
                "Unable to find suitable setter for property %s for class %s",
                $reflectionProperty->getName(),
                $reflectionClass->getName()
            ));
        }

        $classField->setSetter($possibleSetter);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveType(ReflectionProperty $reflectionProperty, ClassField $classField): void
    {
        $type = $reflectionProperty->getType();
        if (null === $type) {
            $classField->setType('mixed');

            return;
        }
        $typeName = $type->getName();
        $isNested = false;
        $isList = false;
        $isCollection = false;
        if (!$type->isBuiltin()) {
            $isNested = true;
        } else if ('array' === $type->getName()) {
            $attributes = $reflectionProperty->getAttributes(Collection::class);
            $isList = true;
            if (!empty($attributes)) {
                $isNested = true;
                $isCollection = true;
                /** @var Collection $attribute */
                $attribute = $attributes[0]->newInstance();
                $typeName = $attribute->getClass();
            }
        }

        $classField->setType($typeName);
        $classField->setIsList($isList);
        $classField->setIsTypedCollection($isCollection);
        if ($isNested) {
            if ($typeName === $reflectionProperty->class) {
                //prevent loop on nested elements of the same type
                return;
            }
            $classField->setClassInfo($this->introspect($typeName));
        }
    }

    /**
     * There are should be 3 ways how to figure out whether the field is required or not.
     * 1. Use attribute #Required. The most preferable way
     * 2. If value has default value it means that fields isn't required
     * 3. Use property typehint e.g
     * ```
     * private int $value
     * ```
     * would be treated as required and
     * ```
     * private ?int $value
     * ```
     * would br treated as optional(non required) field
     * 4. phpDoc comment. If doc comment contains `@var` type and the type contains `null|...` or `...|null' that would
     * be treated as optional, otherwise as required.
     *
     * If all three ways doesn't give a result the field would be treated as optional.
     *
     * @return bool
     */
    private function isRequired(ReflectionProperty $reflectionProperty): bool
    {
        $attributes = $reflectionProperty->getAttributes(Required::class);
        if (!empty($attributes)) {
            return true;
        }

        $propertyType = $reflectionProperty->getType();
        //if property hasn't type hint by default it's equal to null
        if (null !== $propertyType && $reflectionProperty->hasDefaultValue()) {
            return false;
        }

        if (null !== $propertyType) {
            return !$propertyType->allowsNull();
        }

        $doc = $reflectionProperty->getDocComment();
        if (false === $doc) {
            return false;
        }

        if (preg_match('/@var\s+(\S+)/', $doc, $matches)) {
            [, $type] = $matches;

            return !(str_contains($type, 'null|') or str_contains($type, '|null'));
        }

        return false;
    }

    private function findUnresolved(ClassInfo $classInfo, array $map): void
    {
        foreach ($classInfo->getFields() as $field) {
            if ($field->getClassInfo() === null) {

                if (!array_key_exists($field->getType(), $map)) {
                    continue;
                }

                $field->setClassInfo($map[$field->getType()]);
            } else if ($field->getClassInfo() !== null) {
                $this->findUnresolved($field->getClassInfo(), $map);
            }
        }
    }

    public function fixCircularReferences(ClassInfo $classInfo): void
    {
        $map = $this->getNestedClassInfos($classInfo);
        $this->findUnresolved($classInfo, $map);
    }
}

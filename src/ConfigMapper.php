<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

use Diezz\YamlToObjectMapper\Attributes\DefaultValueResolver;
use Diezz\YamlToObjectMapper\Resolver\Context;
use Diezz\YamlToObjectMapper\Resolver\ExpressionArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\InstanceArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\ListArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\ScalarArgumentResolver;
use JetBrains\PhpStorm\Pure;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

class ConfigMapper
{
    #[Pure] public static function make(): static
    {
        return new static();
    }

    /**
     * @template T
     *
     * @param class-string<T> $targetClass
     *
     * @throws ReflectionException
     * @throws ValidationException
     * @throws Resolver\ArgumentResolverException
     * @throws Resolver\Parser\SyntaxException
     * @return T
     *
     * @todo     get rid of Reflection exception
     */
    public function mapFromFile(string $targetClass, string $configFile): YamlConfigurable
    {
        $config = Yaml::parseFile($configFile);

        return $this->map($targetClass, $config);
    }

    /**
     * @template T
     *
     * @param class-string<T> $targetClass
     *
     * @throws ValidationException
     * @throws ReflectionException|Resolver\ArgumentResolverException|Resolver\Parser\SyntaxException
     *
     * @return T
     */
    public function map(string $targetClass, array $config): YamlConfigurable
    {
        $instance = new $targetClass;
        $classInfoReflector = new ClassInfoReflector();
        $classInfo = $classInfoReflector->introspect($targetClass);
        $validationResult = $this->validate($classInfo, $config);

        if (!$validationResult->isValid()) {
            throw new ValidationException($validationResult);
        }

        $preMap = $this->getMappingConfig($classInfo, $config, $instance);
        $rootResolver = new InstanceArgumentResolver($preMap);
        $rootResolver->setClassInfo($classInfo);

        return $rootResolver->resolve(new Context($config, $rootResolver));
    }

    /**
     * @template T
     *
     * @param class-string<T> $targetClass
     *
     * @throws ValidationException
     * @throws ReflectionException|Resolver\ArgumentResolverException|Resolver\Parser\SyntaxException
     *
     * @return T
     */
    public function mapFromString(string $targetClass, string $yaml): YamlConfigurable
    {
        $config = Yaml::parse($yaml);

        return $this->map($targetClass, $config);
    }

    /**
     * @throws Resolver\ArgumentResolverException
     * @throws Resolver\Parser\SyntaxException
     */
    private function getMappingConfig(ClassInfo $classInfo, ?array $config, $parentKey = null): array
    {
        $mappingConfig = [];

        if ($config === null) {
            $config = [];
        }

        foreach ($classInfo->getFields() as $field) {
            $fieldName = $field->getName();
            //Skip values that doesn't exist in config file but has a default values
            if (!array_key_exists($fieldName, $config)) {
                if (!$field->hasDefaultValueResolver()) {
                    continue;
                }

                //fallback to defaultValueResolver
                $defaultValueResolver = $field->getDefaultValueResolver();
                $rawValue = match ($defaultValueResolver) {
                    DefaultValueResolver::PARENT_KEY => $parentKey,
                    DefaultValueResolver::NESTED_LIST => $config,
                };
            } else {
                $rawValue = $config[$fieldName];
            }

            $argResolver = null;

            if ($field->isPrimitive()) {
                if (is_bool($rawValue) || is_int($rawValue) || is_array($rawValue)) {
                    $argResolver = new ScalarArgumentResolver($rawValue);
                } else {
                    $argResolver = new ExpressionArgumentResolver($rawValue);
                }
            } else if ($field->isList()) {
                $value = [];
                if ($field->isTypedCollection()) {
                    foreach ($rawValue as $key => $item) {
                        $resolver = new InstanceArgumentResolver($this->getMappingConfig($field->getClassInfo(), $item, $key));
                        $resolver->setClassInfo($field->getClassInfo());
                        $value[] = $resolver;
                    }
                } else {
                    $value = $this->doMapArray($rawValue);
                }

                $argResolver = new ListArgumentResolver($value);
            } else {
                $argResolver = new InstanceArgumentResolver($this->getMappingConfig($field->getClassInfo(), $rawValue, $field->newInstance()));
                $argResolver->setClassInfo($field->getClassInfo());
            }

            $mappingConfig[$fieldName] = $argResolver;
        }

        return $mappingConfig;
    }

    /**
     * @throws Resolver\Parser\SyntaxException
     */
    private function doMapArray(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->doMapArray($value);
            } else {
                $result[$key] = new ExpressionArgumentResolver($value);
            }
        }

        return $result;
    }

    private function validate(ClassInfo $classInfo, ?array $config, ?array $parent = [], ?ConfigValidationResult $validationResult = null): ConfigValidationResult
    {
        if (null === $validationResult) {
            $validationResult = new ConfigValidationResult();
        }

        $pathFunction = static fn(array $path) => implode(".", $path);

        //Check for required fields
        foreach ($classInfo->getFields() as $field) {
            $fieldName = $field->getName();
            $isFieldExistsInConfig = $config !== null && array_key_exists($fieldName, $config);
            $isRequired = $field->isRequired();
            $path = $parent;
            $path[] = $fieldName;

            if ($isRequired && !$isFieldExistsInConfig) {
                if ($field->hasDefaultValueResolver()) {
                    $defaultValueResolver = $field->getDefaultValueResolver();
                    switch ($defaultValueResolver) {
                        case DefaultValueResolver::PARENT_KEY:
                            $parentKeyExists = !empty($parent);
                            if (!$parentKeyExists) {
                                $validationResult->addError($path, sprintf("Field '%s' is required but not found in the config file", $pathFunction($path)));
                            }
                            break;
                        case DefaultValueResolver::NESTED_LIST:
                            if (empty($config) || !is_array($config)) {
                                $validationResult->addError($path, sprintf("Field '%s' is required but not found in the config file", $pathFunction($path)));
                            }
                            break;
                    }
                } else {
                    $validationResult->addError($path, sprintf("Field '%s' is required but not found in the config file", $pathFunction($path)));
                    continue;
                }
            }

            if (null !== $field->getClassInfo()) {
                if ($field->isTypedCollection()) {
                    if ($isRequired === false && !$isFieldExistsInConfig) {
                        continue;
                    }
                    foreach ($config[$fieldName] as $key => $value) {
                        $path[] = $key;
                        $validationResult = self::validate($field->getClassInfo(), $value, $path, $validationResult);
                    }
                } else {
                    $validationResult = self::validate($field->getClassInfo(), $config[$fieldName], $path, $validationResult);
                }
            }
        }

        return $validationResult;
    }
}

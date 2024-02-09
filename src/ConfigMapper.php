<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

use Diezz\YamlToObjectMapper\Attributes\DefaultValueResolver;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverFactory;
use Diezz\YamlToObjectMapper\Resolver\Context;
use Diezz\YamlToObjectMapper\Resolver\InstanceArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\ListArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\Parser\Parser;
use Diezz\YamlToObjectMapper\Resolver\ScalarArgumentResolver;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

class ConfigMapper
{
    private ArgumentResolverFactory $argumentResolverFactory;

    public function __construct(ArgumentResolverFactory $argumentResolverFactory = null)
    {
        if (null === $argumentResolverFactory) {
            $argumentResolverFactory = new ArgumentResolverFactory();
        }
        $this->argumentResolverFactory = $argumentResolverFactory;
    }

    public static function make(): static
    {
        return new static();
    }

    /**
     * Register new custom argument resolver.
     *
     * @param string $resolverAlias     - resolver name
     * @param string $resolverClassName - class name of argument resolver
     *
     * @return void
     */
    public function registerCustomArgumentResolver(string $resolverAlias, string $resolverClassName): void
    {
        $this->argumentResolverFactory->addResolver($resolverAlias, $resolverClassName);
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
     */
    public function mapFromFile(string $targetClass, string $configFile): object
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
    public function map(string $targetClass, array $config): object
    {
        $instance = new $targetClass;
        $classInfoReflector = new ClassInfoReflector();
        $classInfo = $classInfoReflector->introspect($targetClass);
        $validationResult = $this->validate($classInfo, $config);

        if (!$validationResult->isValid()) {
            throw new ValidationException($validationResult);
        }

        $preMap = $this->getMappingConfig($classInfo, $config, $instance);
        $rootResolver = new InstanceArgumentResolver($classInfo, $preMap);

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
    public function mapFromString(string $targetClass, string $yaml): object
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

        foreach ($config as $fieldName => $rawValue) {
            $field = $classInfo->getClassField($fieldName);
            //Skip values that doesn't exist in config file but has a default values
            if ($field !== null && !array_key_exists($fieldName, $config)) {
                if (!$field->hasDefaultValueResolver()) {
                    continue;
                }

                //fallback to defaultValueResolver
                $defaultValueResolver = $field->getDefaultValueResolver();
                $rawValue = match ($defaultValueResolver) {
                    DefaultValueResolver::PARENT_KEY => $parentKey,
                    DefaultValueResolver::NESTED_LIST => $config,
                };
            }

            $argResolver = null;

            if ($field === null) {
                //Not IgnoreUnknown
                if (!$classInfo->isIgnoreUnknown()) {
                    throw new InvalidConfigPathException("Invalid path $fieldName on class {$classInfo->getClassName()}. To prevent this behaviour use #[IgnoreUnknown] on a target class");
                }
                $argResolver = new ScalarArgumentResolver($rawValue);
            } else if ($field->isPrimitive()) {
                if (is_bool($rawValue) || is_int($rawValue) || is_array($rawValue)) {
                    $argResolver = new ScalarArgumentResolver($rawValue);
                } else {
                    $argResolver = $this->processExpression($rawValue);
                }
            } else if ($field->isList()) {
                $value = [];
                if ($field->isTypedCollection()) {
                    foreach ($rawValue as $key => $item) {
                        $value[] = new InstanceArgumentResolver($field->getClassInfo(), $this->getMappingConfig($field->getClassInfo(), $item, $key));
                    }
                } else {
                    $value = $this->doMapArray($rawValue);
                }

                $argResolver = new ListArgumentResolver($value);
            } else {
                $argResolver = new InstanceArgumentResolver($field->getClassInfo(), $this->getMappingConfig($field->getClassInfo(), $rawValue, $field->newInstance()));
            }

            $mappingConfig[$fieldName] = $argResolver;
        }

        return $mappingConfig;
    }

    /**
     * @throws Resolver\Parser\SyntaxException
     */
    private function processExpression(string $expression): ArgumentResolver
    {
        $parser = new Parser($expression);

        return $parser->parse()->toResolver($this->argumentResolverFactory);
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
            } else if (is_string($value)) {
                $result[$key] = $this->processExpression($value);
            } else {
                $result[$key] = new ScalarArgumentResolver($value);
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

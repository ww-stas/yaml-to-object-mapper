<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

use Diezz\YamlToObjectMapper\Attributes\DefaultValueResolver;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolver;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverFactory;
use Diezz\YamlToObjectMapper\Resolver\Context;
use Diezz\YamlToObjectMapper\Resolver\Parser\AST\ASTNode;
use Diezz\YamlToObjectMapper\Resolver\Parser\Parser;
use Diezz\YamlToObjectMapper\Resolver\ScalarArgumentResolver;
use JetBrains\PhpStorm\Pure;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

class ConfigMapper
{
    private ArgumentResolverFactory $argumentResolverFactory;
    private Context $context;

    /**
     * @param ArgumentResolverFactory $argumentResolverFactory
     */
    public function __construct(ArgumentResolverFactory $argumentResolverFactory)
    {
        $this->argumentResolverFactory = $argumentResolverFactory;
    }


    #[Pure] public static function make(): static
    {
        $argumentResolverFactory = new ArgumentResolverFactory();

        return new static($argumentResolverFactory);
    }

    /**
     * @template T
     *
     * @param class-string<T> $targetClass
     *
     * @throws ReflectionException
     * @throws ValidationException
     * @throws Resolver\ArgumentResolverException
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

        $this->context = new Context($config, $classInfo);

        return $this->doMap($classInfo, $config, $instance);
    }

    /**
     * @template T
     *
     * @param class-string<T> $targetClass
     *
     * @throws ValidationException
     * @throws ReflectionException|Resolver\ArgumentResolverException
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
    private function doMap(ClassInfo $classInfo, ?array $config, $resultInstance, $parentKey = null): YamlConfigurable
    {
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

            if ($field->isPrimitive()) {
                if (is_bool($rawValue) || is_int($rawValue) || is_array($rawValue)) {
                    $value = new ScalarArgumentResolver($rawValue);
                } else {
                    $value = (new Parser($rawValue))->parse();
                }
            } else {
                $targetClassName = $field->getType();
                if ($field->isList()) {
                    $value = [];
                    if ($field->isCollection()) {
                        foreach ($rawValue as $key => $item) {
                            $value[] = $this->doMap($field->getClassInfo(), $item, new $targetClassName, $key);
                        }
                    } else {
                        $value = $this->doMapArray($rawValue);
                    }
                } else {
                    $value = $this->doMap($field->getClassInfo(), $rawValue, $field->newInstance());
                }
            }

            $this->setValue($field, $value, $resultInstance);
        }

        return $resultInstance;
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
                $result[$key] = $this->parseAndResolve($value);
            }
        }

        return $result;
    }

    /**
     * @throws Resolver\Parser\SyntaxException
     */
    private function parseAndResolve($rawValue)
    {
        return (new Parser($rawValue))->parse()->run($this->context);
    }

    private function setValue(ClassField $field, $value, $resultInstance): void
    {
        if ($value instanceof ASTNode) {
            $value = $value->run($this->context);
        }

        if ($value instanceof ArgumentResolver) {
            $value = $value->resolve($this->context);
        }

        if ($value === null && $field->hasDefaultValue()) {
            return;
        }

        if ($field->isPublic()) {
            $resultInstance->{$field->getName()} = $value;
        } else {
            $resultInstance->{$field->getSetter()}($value);
        }
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
                if ($field->isCollection()) {
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

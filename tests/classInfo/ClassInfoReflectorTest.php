<?php declare(strict_types=1);

namespace Test\ClassInfo;

use Diezz\YamlToObjectMapper\Attributes\ResolverType;
use Diezz\YamlToObjectMapper\ClassInfoReflector;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Test\CustomVar\Now\Test01TargetClass;
use Test\Examples\ListModel02;
use Test\Examples\Person;
use Test\Examples\RequiredModel;

/**
 * @covers \Diezz\YamlToObjectMapper\ConfigMapper
 */
class ClassInfoReflectorTest extends TestCase
{
    private ClassInfoReflector $reflector;

    protected function setUp(): void
    {
        $this->reflector = new ClassInfoReflector();
        parent::setUp();
    }

    /**
     * @throws ReflectionException
     */
    public function testShouldReturnCorrectClassInfo(): void
    {
        //Given
        $targetClass = Test01TargetClass::class;

        //When
        $result = $this->reflector->introspect($targetClass);

        //Then
        self::assertNotEmpty($result->getFields());
        $fields = $result->getFields();
        self::assertCount(1, $fields);
        self::assertArrayHasKey('value', $fields);
        $value = $fields['value'];

        self::assertEquals('value', $value->getName());
        self::assertEquals('string', $value->getType());
        self::assertNull($value->getClassInfo());
        self::assertNull($value->getDefaultValueResolver());
        self::assertEquals('setValue', $value->getSetter());
        self::assertTrue($value->isRequired());
        self::assertFalse($value->isPublic());
        self::assertTrue($value->isPrimitive());
        self::assertFalse($value->isList());
        self::assertEquals(ResolverType::EAGER, $value->getArgumentResolverType());
    }

    public function testReflectionOfTypedCollection(): void
    {
        //Given
        $targetClass = ListModel02::class;

        //When
        $result = $this->reflector->introspect($targetClass);

        //Then
        self::assertArrayHasKey('persons', $result->getFields());
        $personField = $result->getFields()['persons'];
        self::assertTrue($personField->isList());
        self::assertEquals(Person::class, $personField->getType());
        self::assertNotNull($personField->getClassInfo());
    }

    public function testRequiredFields(): void
    {
        //Given
        $targetClass = RequiredModel::class;

        //When
        $result = $this->reflector->introspect($targetClass);

        //Then
        self::assertTrue($result->getFields()['value0']->isRequired());
        self::assertTrue($result->getFields()['value1']->isRequired());
        self::assertTrue($result->getFields()['value2']->isRequired());
        self::assertFalse($result->getFields()['value3']->isRequired());
        self::assertFalse($result->getFields()['value4']->isRequired());
        self::assertFalse($result->getFields()['value5']->isRequired());
    }
}

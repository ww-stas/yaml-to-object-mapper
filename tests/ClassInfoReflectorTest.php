<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use Diezz\YamlToObjectMapper\Attributes\DefaultValueResolver;
use Diezz\YamlToObjectMapper\ClassInfoReflector;
use Diezz\YamlToObjectMapper\Tests\Examples\ConcatExample17;
use Diezz\YamlToObjectMapper\Tests\Examples\ListModel01;
use Diezz\YamlToObjectMapper\Tests\Examples\ListModel02;
use Diezz\YamlToObjectMapper\Tests\Examples\ListModelPrivate01;
use Diezz\YamlToObjectMapper\Tests\Examples\ListModelSetterAttribute01;
use Diezz\YamlToObjectMapper\Tests\Examples\Person;
use Diezz\YamlToObjectMapper\Tests\Examples\RequiredModel;
use Diezz\YamlToObjectMapper\Tests\Examples\Table10;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Diezz\YamlToObjectMapper\ClassInfo
 * @covers \Diezz\YamlToObjectMapper\ClassField
 */
class ClassInfoReflectorTest extends TestCase
{
    private ClassInfoReflector $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixture = new ClassInfoReflector();
    }


    public function testListModel(): void
    {
        $classInfo = $this->fixture->introspect(ListModel01::class);

        $fields = $classInfo->getFields();
        self::assertEquals(ListModel01::class, $classInfo->getClassName());
        self::assertArrayHasKey('name', $fields);
        self::assertArrayHasKey('list', $fields);

        $name = $fields['name'];
        self::assertEquals('name', $name->getName());
        self::assertTrue($name->isRequired());
        self::assertTrue($name->isPublic());
        self::assertEquals('string', $name->getType());
        self::assertFalse($name->isList());
        self::assertFalse($name->isTypedCollection());
        self::assertNull($name->getClassInfo());
        self::assertFalse($name->hasDefaultValue());
        self::assertNull($name->getDefaultValueResolver());

        $list = $fields['list'];
        self::assertEquals('list', $list->getName());
        self::assertTrue($list->isRequired());
        self::assertTrue($list->isPublic());
        self::assertTrue($list->isList());
        self::assertFalse($list->isTypedCollection());
        self::assertNull($list->getClassInfo());
        self::assertEquals('array', $list->getType());
    }


    /**
     * @covers \Diezz\YamlToObjectMapper\Attributes\Collection
     */
    public function testTypedCollection(): void
    {
        $classInfo = $this->fixture->introspect(ListModel02::class);

        $fields = $classInfo->getFields();
        self::assertEquals(ListModel02::class, $classInfo->getClassName());
        self::assertArrayHasKey('name', $fields);
        self::assertArrayHasKey('persons', $fields);

        $name = $fields['name'];
        self::assertTrue($name->isRequired());
        self::assertTrue($name->isPublic());
        self::assertEquals('string', $name->getType());
        self::assertFalse($name->isList());
        self::assertFalse($name->isTypedCollection());
        self::assertNull($name->getClassInfo());
        self::assertFalse($name->hasDefaultValue());
        self::assertNull($name->getDefaultValueResolver());

        $person = $fields['persons'];
        self::assertTrue($person->isRequired());
        self::assertTrue($person->isTypedCollection());
        self::assertEquals(Person::class, $person->getType());
        self::assertNotNull($person->getClassInfo());
        self::assertFalse($person->hasDefaultValue());
        self::assertNull($person->getDefaultValueResolver());
    }

    /**
     * @covers \Diezz\YamlToObjectMapper\Attributes\IgnoreUnknown
     */
    public function testIgnoreIgnoreUnknown(): void
    {
        $classInfo = $this->fixture->introspect(ConcatExample17::class);

        self::assertTrue($classInfo->isIgnoreUnknown());
    }

    /**
     * @covers \Diezz\YamlToObjectMapper\Attributes\Required
     * @covers \Diezz\YamlToObjectMapper\Attributes\HasNotDefaultValue
     */
    public function testRequiredFields(): void
    {
        $classInfo = $this->fixture->introspect(RequiredModel::class);

        $value0 = $classInfo->getFields()['value0'];
        self::assertTrue($value0->isRequired());
        self::assertFalse($value0->hasDefaultValue());

        $value1 = $classInfo->getFields()['value1'];
        self::assertTrue($value1->isRequired());
        self::assertFalse($value1->hasDefaultValue());

        $value2 = $classInfo->getFields()['value2'];
        self::assertTrue($value2->isRequired());
        self::assertFalse($value2->hasDefaultValue());

        $value3 = $classInfo->getFields()['value3'];
        self::assertFalse($value3->isRequired());
        self::assertTrue($value3->hasDefaultValue());

        $value4 = $classInfo->getFields()['value4'];
        self::assertFalse($value4->isRequired());
        self::assertFalse($value4->hasDefaultValue());

        $value5 = $classInfo->getFields()['value5'];
        self::assertFalse($value5->isRequired());
        self::assertTrue($value5->hasDefaultValue());
    }

    public function testPrivateSetter(): void
    {
        $classInfo = $this->fixture->introspect(ListModelPrivate01::class);

        $name = $classInfo->getFields()['name'];
        self::assertEquals('setName', $name->getSetter());
    }

    /**
     * @covers \Diezz\YamlToObjectMapper\Attributes\Setter
     */
    public function testPrivateSetterWithAttribute(): void
    {
       $classInfo = $this->fixture->introspect(ListModelSetterAttribute01::class);

        $name = $classInfo->getFields()['name'];
        self::assertEquals('setFoo', $name->getSetter());
    }

    /**
     * @covers \Diezz\YamlToObjectMapper\Attributes\DefaultValueResolver
     */
    public function testDefaultValueResolver():void
    {
        $classInfo = $this->fixture->introspect(Table10::class);

        $name = $classInfo->getFields()['name'];
        self::assertTrue($name->hasDefaultValueResolver());
        self::assertEquals(DefaultValueResolver::PARENT_KEY, $name->getDefaultValueResolver());

        $columns = $classInfo->getFields()['columns'];
        self::assertTrue($columns->hasDefaultValueResolver());
        self::assertEquals(DefaultValueResolver::NESTED_LIST, $columns->getDefaultValueResolver());
    }
}

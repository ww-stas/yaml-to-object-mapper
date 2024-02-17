<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use Diezz\YamlToObjectMapper\Mapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\Tests\Examples\ListModel01;
use Diezz\YamlToObjectMapper\Tests\Examples\ListModel02;
use Diezz\YamlToObjectMapper\Tests\Examples\ListModelPrivate01;
use Diezz\YamlToObjectMapper\Tests\Examples\ListModelSetterAttribute01;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @covers \Diezz\YamlToObjectMapper\Mapper
 * @covers \Diezz\YamlToObjectMapper\ClassInfo
 * @covers \Diezz\YamlToObjectMapper\Resolver\ListArgumentResolver
 */
class ListMappingTest extends TestCase
{

    /**
     * @throws ReflectionException
     * @throws ValidationException
     */
    public function testListMapping(): void
    {
        //Given
        $file = __DIR__ . '/Examples/01-list.yml';

        //When
        $result = Mapper::make()->mapFromFile(ListModel01::class, $file);

        //Then
        self::assertEquals('test mapping of lists', $result->name);
        $list = $result->list;
        self::assertCount(3, $list);
        self::assertArrayHasKey('one', $list);
        self::assertEquals('one', $list['one']);
        self::assertArrayHasKey('two', $list);
        self::assertEquals('two', $list['two']);
        self::assertArrayHasKey('three', $list);
        self::assertEquals($result->name, $list['three']);
    }


    public function testListMappingWithPrivateFields(): void
    {
        //Given
        $file = __DIR__ . '/Examples/01-list.yml';

        //When
        $result = Mapper::make()->mapFromFile(ListModelPrivate01::class, $file);

        //Then
        self::assertEquals('test mapping of lists', $result->getName());
        $list = $result->getList();
        self::assertCount(3, $list);
        self::assertArrayHasKey('one', $list);
        self::assertEquals('one', $list['one']);
        self::assertArrayHasKey('two', $list);
        self::assertEquals('two', $list['two']);
        self::assertArrayHasKey('three', $list);
        self::assertEquals($result->getName(), $list['three']);
    }

    public function testListMappingWithSetterAttribute(): void
    {
        //Given
        $file = __DIR__ . '/Examples/01-list.yml';

        //When
        $result = Mapper::make()->mapFromFile(ListModelSetterAttribute01::class, $file);

        //Then
        self::assertEquals('test mapping of lists', $result->getName());
        $list = $result->getList();
        self::assertCount(3, $list);
        self::assertArrayHasKey('one', $list);
        self::assertEquals('one', $list['one']);
        self::assertArrayHasKey('two', $list);
        self::assertEquals('two', $list['two']);
        self::assertArrayHasKey('three', $list);
        self::assertEquals($result->getName(), $list['three']);
    }

    /**
     * @throws ReflectionException
     * @throws ArgumentResolverException
     * @throws ValidationException
     */
    public function testListMappingWithNestedSubclasses(): void
    {
        //Given
        $file = __DIR__ . '/Examples/02-list.yml';

        //When
        $result = Mapper::make()->mapFromFile(ListModel02::class, $file);

        //Then
        $persons = $result->persons;
        self::assertNotEmpty($persons);
        self::assertCount(2, $persons);
        $firstPerson = $persons[0];
        self::assertEquals('John Smith', $firstPerson->name);
        self::assertEquals(27, $firstPerson->age);
        $secondPerson = $persons[1];
        self::assertEquals('Sarah Connor', $secondPerson->name);
        self::assertEquals(35, $secondPerson->age);
    }
}

<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\ConfigMapper;
use PHPUnit\Framework\TestCase;
use Test\Examples\ListModel;
use Test\Examples\ListModel02;

class ListMappingTest extends TestCase
{

    /**
     * @throws \ReflectionException
     * @throws \Diezz\YamlToObjectMapper\ValidationException
     */
    public function testListMapping(): void
    {
        //Given
        $file = __DIR__ . '/examples/01-list.yml';

        //When
        $result = ConfigMapper::make()->mapFromFile(ListModel::class, $file);

        //Then
        self::assertEquals('test mapping of lists', $result->name);
        $list = $result->list;
        self::assertCount(3, $list);
        self::assertArrayHasKey('one', $list);
        self::assertEquals('one', $list['one']);
        self::assertArrayHasKey('two', $list);
        self::assertEquals('two', $list['two']);
        self::assertArrayHasKey('three', $list);
        self::assertEquals('three', $list['three']);
    }

    public function testListMappingWithNestedSubclasses(): void
    {
        //Given
        $file = __DIR__ . '/examples/02-list.yml';

        //When
        $result = ConfigMapper::make()->mapFromFile(ListModel02::class, $file);

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
<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use  Diezz\YamlToObjectMapper\Mapper;
use PHPUnit\Framework\TestCase;
use Diezz\YamlToObjectMapper\Tests\Examples\DatabaseSchema09;
use Diezz\YamlToObjectMapper\Tests\Examples\DatabaseSchema10;

/**
 * @covers \Diezz\YamlToObjectMapper\Mapper
 */
class DefaultValueResolverTest extends TestCase
{
    public function testMappingTable(): void
    {
        //Given
        $file = __DIR__ . '/examples/09-default-value-resolver.yml';

        //When
        $result = Mapper::make()->mapFromFile(DatabaseSchema09::class, $file);

        //Then
        self::assertCount(2, $result->tables);
        [$firstTable, $secondTable] = $result->tables;
        self::assertEquals('users', $firstTable->name);
        self::assertEquals('orders', $secondTable->name);
    }

    public function testMappingTableWithDefaultValueResolver(): void
    {
        //Given
        $file = __DIR__ . '/examples/10-default-value-resolver.yml';

        //When
        $result = Mapper::make()->mapFromFile(DatabaseSchema10::class, $file);

        //Then
        self::assertCount(2, $result->tables);
        [$firstTable, $secondTable] = $result->tables;
        self::assertEquals('users', $firstTable->name);
        self::assertEquals('orders', $secondTable->name);
    }
}

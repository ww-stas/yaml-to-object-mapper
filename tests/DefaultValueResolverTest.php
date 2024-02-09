<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\ConfigMapper;
use PHPUnit\Framework\TestCase;
use Test\Examples\DatabaseSchema09;
use Test\Examples\DatabaseSchema10;

class DefaultValueResolverTest extends TestCase
{
    public function testMappingTable(): void
    {
        //Given
        $file = __DIR__ . '/examples/09-default-value-resolver.yml';

        //When
        $result = ConfigMapper::make()->mapFromFile(DatabaseSchema09::class, $file);

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
        $result = ConfigMapper::make()->mapFromFile(DatabaseSchema10::class, $file);

        //Then
        self::assertCount(2, $result->tables);
        [$firstTable, $secondTable] = $result->tables;
        self::assertEquals('users', $firstTable->name);
        self::assertEquals('orders', $secondTable->name);
    }
}

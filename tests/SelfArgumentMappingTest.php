<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\Resolver\CircularDependencyException;
use PHPUnit\Framework\TestCase;
use Diezz\YamlToObjectMapper\Tests\Examples\Self03;
use Diezz\YamlToObjectMapper\Tests\Examples\Self04;
use Diezz\YamlToObjectMapper\Tests\Examples\Self05;
use Diezz\YamlToObjectMapper\Tests\Examples\Self12;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\SelfArgumentResolver
 */
class SelfArgumentMappingTest extends TestCase
{
    public function testSelfArgumentResolver(): void
    {
        //Given
        $file = __DIR__ . '/examples/03-self.yml';

        //When
        $result = ConfigMapper::make()->mapFromFile(Self03::class, $file);

        //Then
        self::assertEquals($result->name, $result->target);
    }

    public function testSelfArgumentResolverOfNestedField(): void
    {
        //Given
        $file = __DIR__ . '/examples/04-self.yml';

        //When
        $result = ConfigMapper::make()->mapFromFile(Self04::class, $file);

        //Then
        $secondPerson = $result->persons[1];
        self::assertEquals($secondPerson->name, $result->target);
    }

    public function testSelfArgumentResolverOfNestedFieldShouldThrowAnException(): void
    {
        //Given
        $file = __DIR__ . '/examples/05-self.yml';
        $this->expectException(ArgumentResolverException::class);

        ConfigMapper::make()->mapFromFile(Self04::class, $file);
    }

    public function testSelfArgumentResolverOnFieldThatShouldBeResolvedAsWell(): void
    {
        //Given
        $file = __DIR__ . '/examples/06-self.yml';
        $expectedResult = (new \DateTime())->format('Y-m-d');

        //When
        $result = ConfigMapper::make()->mapFromFile(Self05::class, $file);

        //Then
        self::assertEquals($expectedResult, $result->target);
        self::assertEquals($result->date, $result->target);
    }

    public function testSelfArgumentResolverWithCircularReferences(): void
    {
        //Given
        $file = __DIR__ . '/examples/12-self-circular.yml';
        $this->expectException(CircularDependencyException::class);

        //When && Then
        ConfigMapper::make()->mapFromFile(Self12::class, $file);
    }

    public function testSelfArgumentResolverWithCircularReferences2(): void
    {
        //Given
        $file = __DIR__ . '/examples/13-self-selfRefernce.yml';
        $this->expectException(CircularDependencyException::class);

        //When && Then
        ConfigMapper::make()->mapFromFile(Self12::class, $file);
    }
}

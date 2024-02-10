<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\CustomVar\Now;

use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\NowArgumentResolver
 */
class NowCustomVarResolverTest extends TestCase
{
    /**
     *
     * @throws ReflectionException
     * @throws ValidationException
     * @throws ArgumentResolverException
     */
    public function testShouldReturnCurrentDate(): void
    {
        //Given
        $file = __DIR__ . '/test01.yml';
        $mapper = ConfigMapper::make();
        $expectedResult = (new \DateTime())->format('Y-m-d');

        //When
        $result = $mapper->mapFromFile(Test01TargetClass::class, $file);

        //Then
        self::assertEquals($expectedResult, $result->getValue());
    }
}

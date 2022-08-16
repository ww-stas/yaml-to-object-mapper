<?php declare(strict_types=1);

namespace Test\CustomVar\Now;

use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class NowCustomVarResolverTest extends TestCase
{
    /**
     *
     * @throws ReflectionException
     * @throws ValidationException
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

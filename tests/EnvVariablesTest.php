<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use Diezz\YamlToObjectMapper\Mapper;
use PHPUnit\Framework\TestCase;
use Diezz\YamlToObjectMapper\Tests\Examples\EnvVariable11;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\EnvironmentArgumentResolver
 */
class EnvVariablesTest extends TestCase
{
    protected function setUp():void
    {
        putenv("TEST_VALUE=some_value");
        parent::setUp();
    }

    public function testReadingEnvVariable(): void
    {
        //Given
        $file = __DIR__ . '/Examples/11-env-variables.yml';

        //When
        $result = Mapper::make()->mapFromFile(EnvVariable11::class, $file);

        //Then
        self::assertEquals('Test env variables', $result->name);
        self::assertEquals('some_value', $result->target);
    }
}

<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\ConfigMapper;
use PHPUnit\Framework\TestCase;
use Test\Examples\EnvVariable11;

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
        $file = __DIR__ . '/examples/11-env-variables.yml';

        //When
        $result = ConfigMapper::make()->mapFromFile(EnvVariable11::class, $file);

        //Then
        self::assertEquals('Test env variables', $result->name);
        self::assertEquals('some_value', $result->target);
    }
}

<?php declare(strict_types=1);

namespace Test\Config;

use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Test\Config\Model\Example1\Config;
use Test\Config\Model\Example1\Nested;

class TestConfig extends TestCase
{
    /**
     * @throws ValidationException
     * @throws ReflectionException
     */
    public function testParseNestedObjects(): void
    {
        $class = Config::class;

        $t = new Config();
        $t->setNested((new Nested())->setField("value"));

        /** @var Config $result */
        $result = ConfigMapper::make()->mapFromFile($class, __DIR__ . '/model/example1/config.yml');

        self::assertInstanceOf($class, $result);
        self::assertNotNull($result->getNested());
        self::assertEquals('value', $result->getNested()->getField());
    }
}



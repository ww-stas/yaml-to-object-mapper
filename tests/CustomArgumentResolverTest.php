<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use Diezz\YamlToObjectMapper\Mapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use Diezz\YamlToObjectMapper\Tests\Examples\CustomArgumentResolver15;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\CustomArgumentResolver
 * @covers \Diezz\YamlToObjectMapper\Mapper
 */
class CustomArgumentResolverTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws SyntaxException
     * @throws ValidationException
     * @throws ArgumentResolverException
     */
    public function testCustomArgumentResolver(): void
    {
        $file = __DIR__ . '/Examples/15-custom-resolver.yml';

        $mapper = Mapper::make();
        //Register custom argument resolver
        $mapper->registerCustomArgumentResolver('foo', FooArgumentResolver::class);
        $mapper->registerCustomArgumentResolver('sum', SumArgumentResolver::class);

        $config = $mapper->mapFromFile(CustomArgumentResolver15::class, $file);

        self::assertEquals('foobar', $config->value);
        self::assertEquals(300, $config->total);
    }
}

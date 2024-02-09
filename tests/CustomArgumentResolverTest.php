<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use Test\Examples\CustomArgumentResolver15;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\CustomArgumentResolver
 * @covers \Diezz\YamlToObjectMapper\ConfigMapper
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
        $file = __DIR__ . '/examples/15-custom-resolver.yml';

        $mapper = ConfigMapper::make();
        //Register custom argument resolver
        $mapper->registerCustomArgumentResolver('foo', FooArgumentResolver::class);
        $mapper->registerCustomArgumentResolver('sum', SumArgumentResolver::class);

        $config = $mapper->mapFromFile(CustomArgumentResolver15::class, $file);

        self::assertEquals('foobar', $config->value);
        self::assertEquals(300, $config->total);
    }
}

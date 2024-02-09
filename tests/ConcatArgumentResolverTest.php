<?php declare(strict_types=1);


use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use Test\Examples\ConcatExample17;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\ConcatArgumentResolver
 * @covers \Diezz\YamlToObjectMapper\ConfigMapper
 */
class ConcatArgumentResolverTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws SyntaxException
     * @throws ValidationException
     * @throws ArgumentResolverException
     */
    public function testSubstringArgumentResolver(): void
    {
        $file = __DIR__ . '/examples/17-concat-resolver.yml';

        $mapper = ConfigMapper::make();
        $config = $mapper->mapFromFile(ConcatExample17::class, $file);

        self::assertEquals('some-string-arg1-arg2', $config->value);
    }
}

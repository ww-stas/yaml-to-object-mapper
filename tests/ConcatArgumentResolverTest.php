<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use Diezz\YamlToObjectMapper\Mapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;
use Diezz\YamlToObjectMapper\Tests\Examples\ConcatExample17;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\ConcatArgumentResolver
 * @covers \Diezz\YamlToObjectMapper\Mapper
 * @covers \Diezz\YamlToObjectMapper\ClassInfo
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
        $file = __DIR__ . '/Examples/17-concat-resolver.yml';

        $mapper = Mapper::make();
        $config = $mapper->mapFromFile(ConcatExample17::class, $file);

        self::assertEquals('some-string-arg1-arg2', $config->value);
    }
}

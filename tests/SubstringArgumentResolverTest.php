<?php declare(strict_types=1);


use Diezz\YamlToObjectMapper\Mapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use Diezz\YamlToObjectMapper\Tests\Examples\SubstringExample16;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\SubstringArgumentResolver
 * @covers \Diezz\YamlToObjectMapper\Resolver\CustomArgumentResolver
 * @covers \Diezz\YamlToObjectMapper\Resolver\ArgumentResolver
 * @covers \Diezz\YamlToObjectMapper\Mapper
 */
class SubstringArgumentResolverTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws SyntaxException
     * @throws ValidationException
     * @throws ArgumentResolverException
     */
    public function testSubstringArgumentResolver(): void
    {
        $file = __DIR__ . '/Examples/16-substring-resolver.yml';

        $mapper = Mapper::make();
        $config = $mapper->mapFromFile(SubstringExample16::class, $file);

        self::assertEquals('Testing', $config->value);
        self::assertEquals('SubstringArgumentResolver', $config->anotherValue);
    }
}

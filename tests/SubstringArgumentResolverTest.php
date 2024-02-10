<?php declare(strict_types=1);


use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverException;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use Diezz\YamlToObjectMapper\Tests\Examples\SubstringExample16;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\SubstringArgumentResolver
 * @covers \Diezz\YamlToObjectMapper\ConfigMapper
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
        $file = __DIR__ . '/examples/16-substring-resolver.yml';

        $mapper = ConfigMapper::make();
        $config = $mapper->mapFromFile(SubstringExample16::class, $file);

        self::assertEquals('Testing', $config->value);
        self::assertEquals('SubstringArgumentResolver', $config->anotherValue);
    }
}

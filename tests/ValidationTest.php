<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use Diezz\YamlToObjectMapper\Tests\Examples\Required08;
use Diezz\YamlToObjectMapper\Tests\Examples\Required09;

/**
 * @covers \Diezz\YamlToObjectMapper\ConfigMapper
 */
class ValidationTest extends TestCase
{
    public function testRequiredValidationOnMissedField(): void
    {
        $file = __DIR__ . '/examples/08-required-validation.yml';
        $this->expectException(ValidationException::class);

        ConfigMapper::make()->mapFromFile(Required08::class, $file);
    }

    public function testValidationOnMissedFieldForNullableField(): void
    {
        //Given
        $file = __DIR__ . '/examples/08-required-validation.yml';

        //When
        $result = ConfigMapper::make()->mapFromFile(Required09::class, $file);

        //Then
        self::assertNull($result->target);
    }
}

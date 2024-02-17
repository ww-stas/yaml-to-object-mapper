<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests;

use Diezz\YamlToObjectMapper\Mapper;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use Diezz\YamlToObjectMapper\Tests\Examples\Required08;
use Diezz\YamlToObjectMapper\Tests\Examples\Required09;

/**
 * @covers \Diezz\YamlToObjectMapper\Mapper
 */
class ValidationTest extends TestCase
{
    public function testRequiredValidationOnMissedField(): void
    {
        $file = __DIR__ . '/Examples/08-required-validation.yml';
        $this->expectException(ValidationException::class);

        Mapper::make()->mapFromFile(Required08::class, $file);
    }

    public function testValidationOnMissedFieldForNullableField(): void
    {
        //Given
        $file = __DIR__ . '/Examples/08-required-validation.yml';

        //When
        $result = Mapper::make()->mapFromFile(Required09::class, $file);

        //Then
        self::assertNull($result->target);
    }
}

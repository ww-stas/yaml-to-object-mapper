<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use DateTime;
use PHPUnit\Framework\TestCase;

class VarArgumentResolverTest extends TestCase
{
    public function test01NowResolverShouldResolveCurrentDate(): void
    {
        //Given
        $expected = (new DateTime())->format('Y-m-d H:i:s');
        $resolver = new VarArgumentResolver('now', 'Y-m-d H:i:s');

        //When
        $actual = $resolver->resolve([]);

        //Then
        self::assertEquals($expected, $actual);
    }
}

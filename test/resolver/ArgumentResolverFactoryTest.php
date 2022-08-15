<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use PHPUnit\Framework\TestCase;

class ArgumentResolverFactoryTest extends TestCase
{
    public function test01ShouldCreateFakerResolverUuid(): void
    {
        $result = ArgumentResolver::make('faker::uuid');

        self::assertInstanceOf(FakerArgumentResolver::class, $result);
        self::assertEquals('uuid', $result->getMethod());
    }

    public function test02ShouldCreateFakerResolverCompany(): void
    {
        $result = ArgumentResolver::make('faker::company');

        self::assertInstanceOf(FakerArgumentResolver::class, $result);
        self::assertEquals('company', $result->getMethod());
    }

    public function test03ShouldCreateFakerResolverEmail(): void
    {
        $result = ArgumentResolver::make('faker::email');

        self::assertInstanceOf(FakerArgumentResolver::class, $result);
        self::assertEquals('email', $result->getMethod());
    }

    public function test04ShouldCreateFakerResolverAddress(): void
    {
        $result = ArgumentResolver::make('faker::address');

        self::assertInstanceOf(FakerArgumentResolver::class, $result);
        self::assertEquals('address', $result->getMethod());
    }

    public function test05ShouldCreateFakerResolverRandomElement(): void
    {
        $result = ArgumentResolver::make('faker::randomElement::[NEW, APPROVED]');

        self::assertInstanceOf(FakerArgumentResolver::class, $result);
        self::assertEquals('randomElement', $result->getMethod());
        self::assertEquals([['NEW', 'APPROVED']], $result->getArgument());
    }

    public function test06ShouldCreateVarResolver(): void
    {
        $result = ArgumentResolver::make('$var::now::Y-m-d H:i:s');

        self::assertInstanceOf(VarArgumentResolver::class, $result);
        self::assertEquals('now', $result->getMethod());
        self::assertEquals('Y-m-d H:i:s', $result->getArgument());
    }

    public function test07ShouldCreateFakerResolverDate(): void
    {
        $result = ArgumentResolver::make('faker::date::Y-m-d H:i:s');

        self::assertInstanceOf(FakerArgumentResolver::class, $result);
        self::assertEquals('date', $result->getMethod());
        self::assertEquals('Y-m-d H:i:s', $result->getArgument());
    }

    public function test08ShouldCreateReferenceResolver(): void
    {
        $result = ArgumentResolver::make('$ref::agency.id');

        self::assertInstanceOf(RefArgumentResolver::class, $result);
        self::assertEquals('agency.id', $result->getMethod());
        self::assertEquals(null, $result->getArgument());
    }

    public function test09ShouldCreateFakerResolverWithPipeConfiguration(): void
    {
        $result = ArgumentResolver::make('faker::dateTimeBetween::-1 month|format::Y-m-d H:i:s');

        self::assertInstanceOf(FakerArgumentResolver::class, $result);
        self::assertEquals('dateTimeBetween', $result->getMethod());
        self::assertEquals('-1 month', $result->getArgument());
        $after = $result->getAfter();
        self::assertInstanceOf(FormatArgumentResolver::class, $after);
        self::assertEquals('Y-m-d H:i:s', $after->getMethod());
        self::assertEquals(null, $after->getArgument());
    }

    public function test10ShouldCreateOneOfResolver(): void
    {
        $result = ArgumentResolver::make('oneOf::[google, facebook, yelp, instagram]::id');

        self::assertInstanceOf(OneOfArgumentResolver::class, $result);
        self::assertEquals(['google', 'facebook', 'yelp', 'instagram'], $result->getMethod());
//        self::assertEquals('[google, facebook, yelp, instagram]', $result->getMethod());
        self::assertEquals('id', $result->getArgument());
    }

    public function test11ShouldReturnScalarArgumentResolverForIntValue(): void
    {
        $result = ArgumentResolver::make(1);

        self::assertInstanceOf(ScalarArgumentResolver::class, $result);
        self::assertEquals(1, $result->getMethod());
    }

    public function test12ShouldReturnScalarArgumentResolverForBooleanValue(): void
    {
        $result = ArgumentResolver::make(false);

        self::assertInstanceOf(ScalarArgumentResolver::class, $result);
        self::assertEquals(false, $result->getMethod());
    }

    public function test13ShouldReturnScalarArgumentResolverForStringValue(): void
    {
        $result = ArgumentResolver::make('1');

        self::assertInstanceOf(ScalarArgumentResolver::class, $result);
        self::assertEquals('1', $result->getMethod());
    }
}

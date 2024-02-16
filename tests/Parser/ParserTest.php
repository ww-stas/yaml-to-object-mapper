<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Parser;

use Diezz\YamlToObjectMapper\Resolver\Parser\Parser;
use Diezz\YamlToObjectMapper\Resolver\Parser\SyntaxException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Diezz\YamlToObjectMapper\Resolver\Parser\Parser
 */
class ParserTest extends TestCase
{
    /**
     * @throws SyntaxException
     */
    public function testStringWithSpaceShouldBeEvaluatedAsOneStringLiterals(): void
    {
        $string = 'some string';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'  => 'StringLiteral',
                    'value' => 'some string',
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testStringWithSpaceSurroundedWithSingleQuoteShouldBeEvaluatedAsIs(): void
    {
        $string = "'some string'";
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'  => 'StringLiteral',
                    'value' => "'some string'",
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testStringWithDashAfterStringLiteral(): void
    {
        $string = 'some-string';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'  => 'StringLiteral',
                    'value' => 'some-string',
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testStringWithUnderscoreAfterStringLiteral(): void
    {
        $string = 'some_string';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'  => 'StringLiteral',
                    'value' => 'some_string',
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testResolverProvider(): void
    {
        $string = '${provider:argument}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'provider',
                    'arguments' => [
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'argument',
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testResolverProviderWithString(): void
    {
        $string = 'some-string-${provider:argument}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'  => 'StringLiteral',
                    'value' => 'some',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => '-',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => 'string',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => '-',
                ],
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'provider',
                    'arguments' => [
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'argument',
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testNestedResolverProvider(): void
    {
        $string = 'some-string-${provider:${anotherProvider:argument}}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'  => 'StringLiteral',
                    'value' => 'some',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => '-',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => 'string',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => '-',
                ],
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'provider',
                    'arguments' => [
                        [
                            'type'      => 'ResolverExpression',
                            'provider'  => 'anotherProvider',
                            'arguments' => [
                                [
                                    'type'  => 'StringLiteral',
                                    'value' => 'argument',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testTwoResolversInTheExpression(): void
    {
        $string = 'some-string-${provider:argument1}-${anotherProvider:argument2}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'  => 'StringLiteral',
                    'value' => 'some',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => '-',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => 'string',
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => '-',
                ],
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'provider',
                    'arguments' => [
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'argument1',
                        ],
                    ],
                ],
                [
                    'type'  => 'StringLiteral',
                    'value' => '-',
                ],
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'anotherProvider',
                    'arguments' => [
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'argument2',
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testParsingResolverWithTwoArguments(): void
    {
        $string = '${resolver:firstArgument:secondArgument}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'resolver',
                    'arguments' => [
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'firstArgument',
                        ],
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'secondArgument',
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testParsingResolverWithThreeArguments(): void
    {
        $string = '${resolver:firstArgument:secondArgument:thirdArgument}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'resolver',
                    'arguments' => [
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'firstArgument',
                        ],
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'secondArgument',
                        ],
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'thirdArgument',
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testParsingResolverWithArrayArgumentAndArrayAsArrayItem(): void
    {
        $string = '${resolver:[firstArgument, secondArgument, [sub_argument1, sub_argument2]]}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'resolver',
                    'arguments' => [
                        [
                            'type'   => 'ArrayExpression',
                            'values' => [
                                [
                                    'type'  => 'StringLiteral',
                                    'value' => 'firstArgument',
                                ],
                                [
                                    'type'  => 'StringLiteral',
                                    'value' => 'secondArgument',
                                ],
                                [
                                    'type'   => 'ArrayExpression',
                                    'values' => [
                                        [
                                            'type'  => 'StringLiteral',
                                            'value' => 'sub_argument1',
                                        ],
                                        [
                                            'type'  => 'StringLiteral',
                                            'value' => 'sub_argument2',
                                        ],
                                    ],
                                ],
                            ],
                        ],

                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testParsingResolverWithArrayArgument(): void
    {
        $string = '${resolver:[firstArgument, secondArgument, thirdArgument]}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'resolver',
                    'arguments' => [
                        [
                            'type'   => 'ArrayExpression',
                            'values' => [
                                [
                                    'type'  => 'StringLiteral',
                                    'value' => 'firstArgument',
                                ],
                                [
                                    'type'  => 'StringLiteral',
                                    'value' => 'secondArgument',
                                ],
                                [
                                    'type'  => 'StringLiteral',
                                    'value' => 'thirdArgument',
                                ],
                            ],
                        ],

                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testParsingResolverWithArrayArgumentAndResolverAsArgument(): void
    {
        $string = '${resolver:[firstArgument, secondArgument, ${anotherResolver:[1,2,3]}]}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'resolver',
                    'arguments' => [
                        [
                            'type'   => 'ArrayExpression',
                            'values' => [
                                [
                                    'type'  => 'StringLiteral',
                                    'value' => 'firstArgument',
                                ],
                                [
                                    'type'  => 'StringLiteral',
                                    'value' => 'secondArgument',
                                ],
                                [
                                    'type'      => 'ResolverExpression',
                                    'provider'  => 'anotherResolver',
                                    'arguments' => [
                                        [
                                            'type'   => 'ArrayExpression',
                                            'values' => [
                                                [
                                                    'type'  => 'StringLiteral',
                                                    'value' => '1',
                                                ],
                                                [
                                                    'type'  => 'StringLiteral',
                                                    'value' => '2',
                                                ],
                                                [
                                                    'type'  => 'StringLiteral',
                                                    'value' => '3',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],

                    ],
                ],
            ],
        ], $result);
    }

    /**
     * @throws SyntaxException
     */
    public function testParsingResolverWithPathArgument(): void
    {
        $string = '${self:path.1.name}';
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'self',
                    'arguments' => [
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'path.1.name',
                        ],
                    ],
                ],
            ],
        ], $result);
    }


    /**
     * @throws SyntaxException
     */
    public function testArgumentInSingleQuotesShouldBeParsedAsSingleStringLiteral(): void
    {
        $string = "\${now:'Y-m-d'}";
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'now',
                    'arguments' => [
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'Y-m-d',
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    public function testResolverExpressionWithoutArguments(): void
    {
        $string = "\${now}";
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'now',
                    'arguments' => [
                    ],
                ],
            ],
        ], $result);
    }

    public function testShouldParseArgumentResolverAsArgumentOfAnotherResolver(): void
    {
        $string = "\${format:\${now}:'Y-m-d'}";
        $parser = new Parser($string);

        $result = $parser->parse()->toArray();

        $this->assertEquals([
            'type' => 'Expression',
            'body' => [
                [
                    'type'      => 'ResolverExpression',
                    'provider'  => 'format',
                    'arguments' => [
                        [
                            'type'      => 'ResolverExpression',
                            'provider'  => 'now',
                            'arguments' => [],
                        ],
                        [
                            'type'  => 'StringLiteral',
                            'value' => 'Y-m-d',
                        ],
                    ],
                ],
            ],
        ], $result);
    }
}

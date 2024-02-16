<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser;

class Tokenizer
{
    public const T_BEGIN_OF_EXPRESSION = 1;
    public const T_END_OF_EXPRESSION = 2;
    public const T_SEMICOLON = 3;
    public const T_SINGLE_QUOTE = 4;
    public const T_DOUBLE_QUOTE = 5;
    public const T_STRING_LITERAL = 6;
    public const T_QUOTED_STRING_LITERAL = 7;
    public const T_NUMERIC_LITERAL = 8;
    public const T_DASH = 9;
    public const T_UNDERSCORE = 10;
    public const T_ARRAY_BEGIN = 11;
    public const T_ARRAY_END = 12;
    public const T_COMMA = 13;
    public const T_SPACE = 14;
    public const T_DOT = 15;


    private string $string;
    private int $cursor;

    public function __construct(string $string)
    {
        $this->string = $string;
        $this->cursor = 0;
    }

    /**
     * @throws SyntaxException
     */
    public function getNextToken(): ?Token
    {
        if (!$this->hasMoreToken()) {
            return null;
        }

        $string = substr($this->string, $this->cursor);

        foreach ($this->getSpec() as $specItem) {
            $tokenType = $specItem->getTokenType();
            $tokenValue = $this->match($specItem->getPattern(), $string);
            if ($tokenValue === null) {
                continue;
            }

            if ($tokenType === null) {
                return $this->getNextToken();
            }

            return Token::of($tokenType, $tokenValue);
        }

        throw new SyntaxException("Unexpected token ${string[0]}");
    }

    public function isEOF(): bool
    {
        return $this->cursor === strlen($this->string);
    }

    public function isScalar(): bool
    {
        return preg_match("/\\$\{.*\}/", $this->string) === 0;
    }

    public function getString(): string
    {
        return $this->string;
    }

    /**
     * Match a token for a regular expression
     */
    private function match(string $pattern, string $string): ?string
    {
        preg_match($pattern, $string, $matches);
        if (empty($matches)) {
            return null;
        }

        $result = $matches[0];
        $this->cursor += strlen($result);

        return $result;
    }

    private function hasMoreToken(): bool
    {
        return $this->cursor < strlen($this->string);
    }

    /**
     * @return SpecItem[]
     */
    private function getSpec(): array
    {
        return [
            //Skip spaces
            SpecItem::of("/^\s+/", self::T_SPACE),

            //Special chars
            SpecItem::of("/^\\$\{/", self::T_BEGIN_OF_EXPRESSION),
            SpecItem::of("/^\}/", self::T_END_OF_EXPRESSION),
            SpecItem::of("/^\:/", self::T_SEMICOLON),
            SpecItem::of("/^-/", self::T_DASH),
            SpecItem::of("/^\[/", self::T_ARRAY_BEGIN),
            SpecItem::of("/^\]/", self::T_ARRAY_END),
            SpecItem::of("/^\,/", self::T_COMMA),
            SpecItem::of("/^\./", self::T_DOT),

            // Literals
            SpecItem::of("/^\"[^\"]*\"/", self::T_QUOTED_STRING_LITERAL),
            SpecItem::of("/^'[^']*'/", self::T_QUOTED_STRING_LITERAL),
            SpecItem::of("/^\w+/", self::T_STRING_LITERAL),
            SpecItem::of("/^\d+/", self::T_NUMERIC_LITERAL),
        ];
    }

}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser;

class Token
{
    private ?int $tokenType;
    private string $value;

    public function __construct(?int $tokenType, string $value)
    {
        $this->tokenType = $tokenType;
        $this->value = $value;
    }

    public function getTokenType(): ?int
    {
        return $this->tokenType;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function of(?int $tokenType, string $value): static
    {
        return new Token($tokenType, $value);
    }
}

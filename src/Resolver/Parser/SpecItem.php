<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser;

class SpecItem
{
    private string $pattern;
    private ?int $tokenType;

    /**
     * @param string $pattern
     * @param ?int $token
     */
    public function __construct(string $pattern, ?int $token)
    {
        $this->pattern = $pattern;
        $this->tokenType = $token;
    }

    public static function of(string $pattern, ?int $token): self
    {
        return new self($pattern, $token);
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getTokenType(): ?int
    {
        return $this->tokenType;
    }
}

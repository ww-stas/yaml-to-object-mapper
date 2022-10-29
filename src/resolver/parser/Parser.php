<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser;

use JetBrains\PhpStorm\ArrayShape;

class Parser
{
    private Tokenizer $tokenizer;
    private ?Token $lookahead;
    private ?Token $current = null;

    public function __construct(string $string)
    {
        $this->tokenizer = new Tokenizer($string);
    }

    /**
     * @throws SyntaxException
     */
    #[ArrayShape(['type' => "string", 'body' => "array"])]
    public function parse(): array
    {
        $this->lookahead = $this->tokenizer->getNextToken();

        return $this->expression();
    }

    /**
     * @throws SyntaxException
     */
    #[ArrayShape(['type' => "string", 'body' => "array"])]
    private function expression(): array
    {
        return [
            'type' => 'Expression',
            'body' => $this->statementList(),
        ];
    }

    /**
     * @throws SyntaxException
     */
    private function statementList(?int $stopLookahead = null): array
    {
        $list = [$this->statement()];

        while ($this->lookahead !== null && $this->lookahead->getTokenType() !== $stopLookahead) {
            $list[] = $this->statement();
        }

        return $list;
    }

    /**
     * @throws SyntaxException
     */
    private function statement(): array
    {
        switch ($this->lookahead->getTokenType()) {
            case Tokenizer::T_STRING_LITERAL :
                return $this->stringLiteral();
            case Tokenizer::T_QUOTED_STRING_LITERAL:
                return $this->quotedStringLiteral();
            case Tokenizer::T_BEGIN_OF_EXPRESSION:
                return $this->resolverExpression();
            case Tokenizer::T_SEMICOLON:
                return $this->asStringLiteral(Tokenizer::T_SEMICOLON);
            case Tokenizer::T_DASH:
                return $this->asStringLiteral(Tokenizer::T_DASH);
            case Tokenizer::T_UNDERSCORE:
                return $this->asStringLiteral(Tokenizer::T_UNDERSCORE);
            case Tokenizer::T_ARRAY_BEGIN:
                return $this->arrayExpression();
            default:
                throw new SyntaxException("Unexpected literal: {$this->lookahead->getValue()}");
        }
    }

    /**
     * @throws SyntaxException
     */
    #[ArrayShape(['type' => "string", 'values' => "array"])]
    public function arrayExpression(): array
    {
        $this->eat(Tokenizer::T_ARRAY_BEGIN);

        $list = [];

        while ($this->lookahead->getTokenType() !== Tokenizer::T_ARRAY_END) {
            $list[] = $this->statement();
            if ($this->lookahead->getTokenType() === Tokenizer::T_COMMA) {
                $this->eat(Tokenizer::T_COMMA);
            }
        }

        $this->eat(Tokenizer::T_ARRAY_END);

        return [
            'type'   => 'ArrayExpression',
            'values' => $list,
        ];
    }

    /**
     * @throws SyntaxException
     */
    #[ArrayShape(['type' => "string", 'value' => "string"])]
    private function asStringLiteral(int $tokenType): array
    {
        //if ($this->current->getTokenType() === Tokenizer::T_STRING_LITERAL || $this->current->getTokenType() === Tokenizer::T_QUOTED_STRING_LITERAL) {
        $token = $this->eat($tokenType);

        return [
            'type'  => 'StringLiteral',
            'value' => $token->getValue(),
        ];
        //}

        //throw new SyntaxException("Unexpected literal: {$this->lookahead->getValue()}");
    }

    /**
     * @throws SyntaxException
     */
    #[ArrayShape(['type' => "string", 'provider' => "string", 'arguments' => "array[]"])]
    private function resolverExpression(): array
    {
        $this->eat(Tokenizer::T_BEGIN_OF_EXPRESSION);
        $provider = $this->eat(Tokenizer::T_STRING_LITERAL);
        $this->eat(Tokenizer::T_SEMICOLON);
        $arguments = $this->argumentList();
        $this->eat(Tokenizer::T_END_OF_EXPRESSION);

        return [
            'type'      => 'ResolverExpression',
            'provider'  => $provider->getValue(),
            'arguments' => $arguments,
        ];
    }

    /**
     * @throws SyntaxException
     */
    private function argumentList(): array
    {
        $list = [];

        while ($this->lookahead->getTokenType() !== Tokenizer::T_END_OF_EXPRESSION) {
            $list[] = $this->statement();
            if ($this->lookahead->getTokenType() === Tokenizer::T_SEMICOLON) {
                $this->eat(Tokenizer::T_SEMICOLON);
            }
        }

        return $list;
    }

    /**
     * @throws SyntaxException
     */
    #[ArrayShape(['type' => "string", 'value' => "string"])]
    private function stringLiteral(): array
    {
        $token = $this->eat(Tokenizer::T_STRING_LITERAL);

        return [
            'type'  => 'StringLiteral',
            'value' => $token->getValue(),
        ];
    }

    /**
     * @throws SyntaxException
     */
    #[ArrayShape(['type' => "string", 'value' => "string"])]
    private function quotedStringLiteral(): array
    {
        $token = $this->eat(Tokenizer::T_QUOTED_STRING_LITERAL);

        return [
            'type'  => 'StringLiteral',
            'value' => substr($token->getValue(), 1, -1),
        ];
    }

    /**
     * @throws SyntaxException
     */
    private function eat(int $tokenType = null): Token
    {
        $token = $this->lookahead;

        if (null === $token) {
            throw new SyntaxException("Unexpected end of input, expected $tokenType");
        }

        if ($tokenType !== null && $token->getTokenType() !== $tokenType) {
            throw new SyntaxException("Unexpected token: {$token->getValue()}, expected: $tokenType");
        }

        $this->current = $this->lookahead;
        $this->lookahead = $this->tokenizer->getNextToken();

        return $token;
    }
}

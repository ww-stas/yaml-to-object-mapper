<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser;

use Diezz\YamlToObjectMapper\Resolver\Parser\AST\ArrayExpression;
use Diezz\YamlToObjectMapper\Resolver\Parser\AST\ASTNode;
use Diezz\YamlToObjectMapper\Resolver\Parser\AST\Expression;
use Diezz\YamlToObjectMapper\Resolver\Parser\AST\PathArgument;
use Diezz\YamlToObjectMapper\Resolver\Parser\AST\ResolverExpression;
use Diezz\YamlToObjectMapper\Resolver\Parser\AST\StringLiteral;

class Parser
{
    private Tokenizer $tokenizer;
    private ?Token $lookahead;
    private ?Token $current = null;
    private ?string $context = null;

    private const CONTEXT_ARGUMENT_RESOLVER = 'argumentResolver';

    public function __construct(string $string)
    {
        $this->tokenizer = new Tokenizer($string);
    }

    /**
     * @throws SyntaxException
     */
    public function parse(): ASTNode
    {
        $this->lookahead = $this->tokenizer->getNextToken();

        return $this->expression();
    }

    /**
     * @throws SyntaxException
     */
    private function expression(): Expression
    {
        if ($this->tokenizer->isScalar()) {
            $node = [new StringLiteral($this->tokenizer->getString())];
        } else {
            $node = $this->statementList();
        }

        return new Expression($node);
    }

    /**
     * @throws SyntaxException
     * @return ASTNode[]
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
    private function statement(): ASTNode
    {
        if ($this->context === self::CONTEXT_ARGUMENT_RESOLVER) {
            return $this->resolverContext();
        }

        return $this->stringContext();
    }

    /**
     * @throws SyntaxException
     */
    private function resolverContext(): ASTNode
    {
        //In scope of resolver we skip all space symbols, in other cases treat them as StringLiteral
        while ($this->lookahead->getTokenType() === Tokenizer::T_SPACE) {
            $this->eat(Tokenizer::T_SPACE);
        }

        return match ($this->lookahead->getTokenType()) {
            Tokenizer::T_ARRAY_BEGIN => $this->arrayExpression(),
            Tokenizer::T_STRING_LITERAL => $this->argument(),
            Tokenizer::T_BEGIN_OF_EXPRESSION => $this->resolverExpression(),
            Tokenizer::T_QUOTED_STRING_LITERAL => $this->quotedStringLiteral(),
            default => throw new SyntaxException("Unexpected literal: {$this->lookahead->getValue()}"),
        };
    }

    /**
     * @throws SyntaxException
     */
    private function stringContext(): ASTNode
    {
        if ($this->lookahead->getTokenType() === Tokenizer::T_BEGIN_OF_EXPRESSION) {
            return $this->resolverExpression();
        }

        return $this->asStringLiteral($this->lookahead->getTokenType());
    }

    /**
     * @throws SyntaxException
     */
    private function arrayExpression(): ArrayExpression
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

        return new ArrayExpression($list);
    }

    /**
     * @throws SyntaxException
     */
    private function asStringLiteral(int $tokenType): StringLiteral
    {
        return new StringLiteral($this->eat($tokenType)->getValue());
    }

    /**
     * @throws SyntaxException
     */
    private function resolverExpression(): ResolverExpression
    {
        $this->context = self::CONTEXT_ARGUMENT_RESOLVER;
        $this->eat(Tokenizer::T_BEGIN_OF_EXPRESSION);
        $provider = $this->eat(Tokenizer::T_STRING_LITERAL);
        $arguments = [];
        if ($this->lookahead->getTokenType() === Tokenizer::T_SEMICOLON) {
            $this->eat(Tokenizer::T_SEMICOLON);
            $arguments = $this->argumentList();
        }
        $this->eat(Tokenizer::T_END_OF_EXPRESSION);
        $this->context = null;

        return new ResolverExpression($provider->getValue(), $arguments);
    }

    /**
     * @throws SyntaxException
     * @return ASTNode[]
     */
    private function argumentList(): array
    {
        $list = [];

        while ($this->lookahead->getTokenType() !== Tokenizer::T_END_OF_EXPRESSION) {
            $list[] = $this->resolverContext();
            if ($this->lookahead->getTokenType() === Tokenizer::T_SEMICOLON) {
                $this->eat(Tokenizer::T_SEMICOLON);
            }
        }

        return $list;
    }

    /**
     * @throws SyntaxException
     */
    private function argument(): ASTNode
    {
        $token = $this->eat(Tokenizer::T_STRING_LITERAL);
        if ($this->lookahead->getTokenType() === Tokenizer::T_DOT) {
            return $this->pathArgument($token);
        }

        return new StringLiteral($token->getValue());
    }

    /**
     * @throws SyntaxException
     */
    private function pathArgument(Token $token): PathArgument
    {
        $pathArgument = new PathArgument();
        $pathArgument->addPathItem($token->getValue());

        $stop = [Tokenizer::T_ARRAY_END, Tokenizer::T_END_OF_EXPRESSION, Tokenizer::T_COMMA];
        do {
            $this->eat(Tokenizer::T_DOT);
            $token = $this->eat(Tokenizer::T_STRING_LITERAL);
            $pathArgument->addPathItem($token->getValue());
        } while (!in_array($this->lookahead->getTokenType(), $stop, true));

        return $pathArgument;
    }

    /**
     * @throws SyntaxException
     */
    private function stringLiteral(): StringLiteral
    {
        return new StringLiteral($this->eat(Tokenizer::T_STRING_LITERAL)->getValue());
    }

    /**
     * @throws SyntaxException
     */
    private function quotedStringLiteral(): StringLiteral
    {
        $token = $this->eat(Tokenizer::T_QUOTED_STRING_LITERAL);

        return new StringLiteral(substr($token->getValue(), 1, -1));
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

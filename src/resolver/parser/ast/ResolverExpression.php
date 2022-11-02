<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver\Parser\AST;

use Diezz\YamlToObjectMapper\Resolver\ArgumentResolverFactory;
use JetBrains\PhpStorm\ArrayShape;

class ResolverExpression extends ASTNode
{
    /**
     * @param string    $provider
     * @param ASTNode[] $arguments
     */
    public function __construct(
        private string $provider,
        private array  $arguments
    )
    {
    }

    public function getType(): string
    {
        return 'ResolverExpression';
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @return ASTNode[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    #[ArrayShape(['type' => "string", 'provider' => "string", 'arguments' => "array"])]
    public function toArray(): array
    {
        $arguments = [];
        foreach ($this->arguments as $value) {
            $arguments[] = $value->toArray();
        }

        return [
            'type'      => $this->getType(),
            'provider'  => $this->getProvider(),
            'arguments' => $arguments,
        ];
    }

    public function run(mixed $context): mixed
    {
        $argumentResolverFactory = new ArgumentResolverFactory();

        $arguments = [];
        foreach ($this->arguments as $argument) {
            $arguments[] = $argument->run($context);
        }
        $argumentResolver = $argumentResolverFactory->create2($this->getProvider(), $arguments);

        return $argumentResolver->resolve($context);
    }
}

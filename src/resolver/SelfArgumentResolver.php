<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class SelfArgumentResolver extends CustomArgumentResolver
{
    private ScalarArgumentResolver $path;
    private array $visited = [];

    /**
     * @param ScalarArgumentResolver $path
     */
    public function __construct(ScalarArgumentResolver $path)
    {
        $this->path = $path;
    }

    public function setVisited(array $visited): void
    {
        $this->visited = $visited;
    }

    /**
     * @throws ArgumentResolverException
     * @var Context $context
     */
    protected function doResolve($context = null)
    {
        $result = $context->getMappingConfig();
        $path = $this->getPath($context);
        $pathRep = implode('.', $path);

        foreach ($path as $item) {
            $resolver = $result->findByPath($item);
            if ($resolver instanceof SystemArgumentResolver) {
                $result = $resolver;
            } else if ($resolver instanceof CustomArgumentResolver) {
                if ($resolver instanceof self) {
                    if (in_array($item, $this->visited, true)) {
                        throw new CircularDependencyException($this->visited);
                    }
                    $this->visited[] = $item;
                    $resolver->setVisited($this->visited);
                }
                $result = $resolver->resolve($context);
            } else {
                throw new ArgumentResolverException("Path '$pathRep' couldn't be resolved");
            }
        }

        return $result;
    }

    private function getPath($context): array
    {
        return explode(".", $this->path->resolve($context));
    }
}

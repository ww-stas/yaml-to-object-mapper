<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

use RuntimeException;

class SelfArgumentResolver extends CustomArgumentResolver
{
    private static array $visited = [];

    public function init(): void
    {
        parent::init();
        self::$visited = [];
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
                if (in_array($item, self::$visited, true)) {
                    throw new CircularDependencyException(self::$visited);
                }
                self::$visited[] = $item;
                $result = $resolver->resolve($context);
            } else {
                throw new ArgumentResolverException("Path '$pathRep' couldn't be resolved");
            }
        }

        return $result;
    }

    private function getPath($context): array
    {
        if (!($this->rawValue instanceof ScalarArgumentResolver)) {
            throw new RuntimeException("Wrong initialization of SelfArgumentResolver");
        }

        return explode(".", $this->rawValue->resolve($context));
    }

    public function getName(): string
    {
        return 'self';
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class SelfArgumentResolver extends CustomArgumentResolver
{
    /**
     * @throws ArgumentResolverException
     * @var Context $context
     */
    protected function doResolve($context = null)
    {
        if (is_array($this->rawValue)) {
            $path = $this->rawValue;
        } else {
            $path = explode(".", $this->rawValue);
        }
        $pathRep = implode('.', $path);

        $result = $context->getMappingConfig();

        foreach ($path as $item) {
            $resolver = $result->findByPath($item);
            if ($resolver instanceof SystemArgumentResolver) {
                $result = $resolver;
            } else if ($resolver instanceof CustomArgumentResolver) {
                $result = $resolver->resolve($context);
            } else {
                throw new ArgumentResolverException("Path '$pathRep' couldn't be resolved");
            }
        }

        return $result;
    }

    public function getName(): string
    {
        return 'self';
    }
}

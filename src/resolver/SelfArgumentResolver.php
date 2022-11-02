<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class SelfArgumentResolver extends ArgumentResolver
{
    /**
     * @throws ArgumentResolverException
     * @var Context $context
     */
    protected function doResolve($context = null)
    {
        $config = $context->getConfig();
        $argumentResolverFactory = new ArgumentResolverFactory();

        if (is_array($this->method)) {
            $path = $this->method;
        } else {
            $path = explode(".", $this->method);
        }
        $pathRep = implode('.', $path);

        $result = $config;

        foreach ($path as $item) {
            if (!array_key_exists($item, $result)) {
                throw new ArgumentResolverException("Path '$pathRep' couldn't be resolved");
            }

            $result = $argumentResolverFactory->create($result[$item])->resolve($context);
        }

        return $result;
    }

    public function getName(): string
    {
        return 'self';
    }
}

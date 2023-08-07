<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class FormatArgumentResolver extends CustomArgumentResolver
{
    protected function doResolve($context = null)
    {
        //if ($context === null) {
        //    throw new \RuntimeException('The context must not be null');
        //}
        //
        //if (!$context instanceof \DateTime) {
        //    throw new \RuntimeException('The context must be the instance of \DateTime ');
        //}

        return $this->rawValue->format($this->argument);
    }

    public function getName(): string
    {
        return 'format';
    }
}

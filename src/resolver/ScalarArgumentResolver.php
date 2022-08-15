<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Resolver;

class ScalarArgumentResolver extends ArgumentResolver
{
    private string $type;

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    protected function doResolve($context = null)
    {
        $result = $this->method;

        if ($this->type === 'string') {
            return (string)$result;
        }

        return $this->method;
    }
}

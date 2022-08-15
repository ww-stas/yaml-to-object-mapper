<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

class ConfigValidationResult
{
    private array $errors = [];

    public function addError(array $path, $errorMessage): void
    {
        $this->errors[$this->getPath($path)] = $errorMessage;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function getPath(array $path): string
    {
        return implode(".", $path);
    }
}

<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

class ValidationException extends \Exception
{
    private ConfigValidationResult $result;

    public function __construct(ConfigValidationResult $result)
    {
        $this->result = $result;
        parent::__construct((string)$this);
    }

    public function __toString()
    {
        $output = "\nThere are validation errors: \n";
        foreach ($this->result->getErrors() as $error) {
            $output .= " - $error\n";
        }

        return $output;
    }
}

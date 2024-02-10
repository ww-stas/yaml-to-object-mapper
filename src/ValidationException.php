<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper;

class ValidationException extends \Exception
{
    private ValidationResult $result;

    public function __construct(ValidationResult $result)
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

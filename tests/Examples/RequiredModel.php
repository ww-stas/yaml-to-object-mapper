<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Tests\Examples;

use Diezz\YamlToObjectMapper\Attributes\HasNotDefaultValue;
use Diezz\YamlToObjectMapper\Attributes\Required;

class RequiredModel
{
    /**
     * Required field
     */
    #[Required]
    #[HasNotDefaultValue]
    public $value0;

    /**
     * This field is required
     */
    public string $value1;

    /**
     * This field required as well based on type hint int the doc comment
     *
     * @var string
     */
    #[HasNotDefaultValue]
    public $value2;

    /**
     * Field isn't required because it has default value
     */
    public string $value3 = 'value3';

    /**
     * Nullable field isn't required
     */
    public ?string $value4;

    /**
     * Nullable field isn't required
     *
     * @var string|null
     */
    public $value5;
}

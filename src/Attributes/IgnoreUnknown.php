<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Attributes;

use Attribute;

/**
 * Prevents InvalidConfigPathException when a field exists in the YAML config but not in the target class.
 * Mark your target class with this attribute to allow mapping to proceed without throwing this exception
 */
#[Attribute(Attribute::TARGET_CLASS)]
class IgnoreUnknown
{

}

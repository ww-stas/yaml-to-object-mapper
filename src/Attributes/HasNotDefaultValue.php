<?php declare(strict_types=1);

namespace Diezz\YamlToObjectMapper\Attributes;

use Attribute;

/**
 * If #[Required] attribute is uses on property without typehint
 * the Reflection API shows that field has a default values (null).
 * Use this attribute on property to indicate that this property has
 * not a default value.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class HasNotDefaultValue
{

}

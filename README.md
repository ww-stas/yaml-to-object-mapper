Yaml to Object Mapper
=============================
Bridge the Gap Between YAML and PHP Objects
---------------
Effortlessly transform your YAML configurations into robust PHP objects with the Yaml to Object Mapper library. 
Embrace efficient mapping, powerful validation, and flexible variable processing, all while leveraging the 
convenience of PHP 8 attributes.

**Key Features:**
- Seamless Mapping: Effortlessly map hierarchical YAML data to deeply nested PHP objects, saving you 
tedious manual configuration.
- Comprehensive Validation: Ensure data integrity with built-in validation mechanisms like #[Required] attributes, 
type hints, and customizable rules.
- Declarative DSL: Customize attribute-based configuration for fields and validation, reducing boilerplate code 
and promoting readability.
- Variable Processing: Utilize dynamic variables like ${env:DB_PASSWORD} directly within your YAML files,
streamlining configuration management.
- Custom Argument Resolvers: Gain ultimate flexibility by crafting custom logic for processing variables, 
tailored to your specific needs.
- Built-in Variables: Enjoy a range of pre-defined variables like now, self, and format for common tasks 
within your YAML configurations.
- PHP 8 Integration: Leverage the power of PHP 8 attributes for cleaner, more concise code, 
enhancing your development experience.

## Installation
```
composer require diezz/yaml-to-object-mapper
```

## Basic usage
We have a simple `config.yml` file 
```yaml
name: object mapper
connection:
  host: localhost
  port: 3202
  username: test
  password: password
```
which we want to be mapped to the object of class:

```php
class Config {
    public string $name;
    public ConnectionSettings $connection
}

class ConnectionSettings {
    public string $host;
    public string $port;
    public string $username;
    public string $password;
}

$config = Mapper::make()->mapFromFile(Config::class, 'config.yml');
```

## Extended usage

### Dynamic Field Mapping with Default Value Resolvers
Tired of writing redundant property names and nested lists in your YAML? Default value resolvers let you automatically infer 
field names and structure, streamlining your configuration.

Example:

Instead of this verbose YAML:

```yaml
tables:
  - name: users
    columns:
      username: varchar(255)
      email: varchar(255)
      password: varchar(255)

  - name: orders
    columns: 
      id: int
      user_id: int
      price: float
```

```php
use Diezz\YamlToObjectMapper\Attributes\Collection;

class DatabaseSchema {
    #[Collection(class: Table::class)]
    public array $tables;
}

class Table {
    public string $name;
    public array $columns;
}
```
Imagine the conciseness of:

```yaml
tables:
  users:
    username: varchar(255)
    email: varchar(255)
    password: varchar(255)

  orders:
    id: int
    user_id: int
    price: float
```

```php
class Table {
    #[DefaultValueResolver(resolver: DefaultValueResolver::PARENT_KEY)]
    public string $name;
    
    #[DefaultValueResolver(resolver: DefaultValueResolver::NESTED_LIST)]
    public array $columns;
}
```

#### How it works:
- `[DefaultValueResolver(resolver: DefaultValueResolver::PARENT_KEY)]`: Automatically sets the name field of each table object to its key in the YAML list.
- `[DefaultValueResolver(resolver: DefaultValueResolver::NESTED_LIST)]`: Treats each YAML list item as a property of the object and creates an associative array based on the keys.

### Validation
By default, the mapper checks that required fields are present in yml file.
There are multiple ways how the mapper defines which field is required:
1. The most obvious way is mark required field with `#[Required]` attribute.
2. If class field isn't marked with required attribute the mapper checks type hint of the field. It could be a php 7 
type hint or phpdoc comment. Nullable properties or properties initiated with default value are treated as not required 
or vice verse.

Maintain the consistency and accuracy of your configurations with the library's integrated validation mechanisms. 
It offers a flexible approach to define required fields and enforce data types.

How It Works:

- Explicit Marking with `#[Required]`: Clearly indicate mandatory fields using the `#[Required]` attribute. This provides 
the most direct control over validation expectations.
- Type Hints and Doc Comments:
  - PHP 7 Type Hints: Fields with explicit type hints (e.g., public string $name) are implicitly treated as required.
  - Doc Comments: Fields with type hints in their doc comments (e.g., `@var string`) are also considered required.
- Default Values and Nullable Types:
  - Fields with Default Values: Fields assigned a default value in their declaration are not required, as they have a fallback value.
  - Nullable Types: Nullable fields (using a question mark or |null in the type declaration) are not required, as they can accept null as a valid value.


```php
use Diezz\YamlToObjectMapper\Attributes\Required;

class Model {
    /**
     * Explicitly required field
     */
    #[Required]
    public $value0;

    /**
     * Required due to string type hint
     */
    public string $value1;

    /**
     * Required based on type hint in doc comment
     *
     * @var string
     */
    public $value2;

    /**
     * Not required due to default value
     */
    public string $value3 = 'value3';

    /**
     * Not required due to nullable type hint
     */
    public ?string $value4;

    /**
     * Not required due to nullable type hint in doc comment
     *
     * @var string|null
     */
    public $value5;
}
```

### Dynamic Configuration with Variable Processing

Unleash the full potential of your YAML configurations with dynamic variable processing. 
The library supports various built-in variables and even allows you to create custom logic for advanced scenarios

**Built-in variables:**
- `self` - Access values within the same configuration object, enabling self-referencing and cross-property dependencies. `${self::connection.host}`
- `substring` - Extract a portion of a string, providing string manipulation capabilities `${substring:${self:name}:7}`
- `now` - Retrieve the current date and time, enabling dynamic timestamps and time-based configurations `${now}`
- `format` - Format dates and times according to specified patterns, ensuring consistent date/time representations.  `${format:${now}:Y-M-D}`
- `env` - Read environment variables, securely integrating configuration with environment-specific settings `${env:DB_PASSWORD}`

**Syntax:**

`${varName:firstArgument:secondArgument}`

Another variable could be passed as an argument of another one, for example:

`${varName1:${varName2:argument1}:argument2}`

### Custom Argument Resolvers

For even more flexibility, create custom argument resolvers to handle unique variable processing needs.

**Steps:**
- Create a Custom Resolver Class: Extend the CustomArgumentResolver class and implement the doResolve method to define the variable's logic.
- Register the Resolver: Use the Mapper::registerCustomArgumentResolver method to associate the resolver with a variable name.
- Utilize the Variable in YAML: Use the registered variable within your YAML configuration, passing arguments as needed.


In top of variable processing there is an ArgumentResolver. 
You can create your own ArgumentResolvers which gives an ability of processing custom variables in your yaml files. 
To do so create a class extending `CustomArgumentResolver`. Constructor of your CustomArgumentResolver implementation
should accept `ArgumentResolver` which is resolver for argument of the variable. Count of params in the constuctor
should be equals to count of argument supporting by the variable.

```php
class SumArgumentResolver extends CustomArgumentResolver
{
    private array $arguments;

    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
    }

    protected function doResolve($context = null): int
    {
        $sum = 0;
        foreach ($this->arguments as $iValue) {
            $sum += $iValue->resolve($context);
        }

        return $sum;
    }
}

$mapper = Mapper::make();
//Register the resolver
$mapper->registerCustomArgumentResolver('sum', SumArgumentResolver::class);
$result = $mapper->map($file, Output::class);
```
and now you can use inside yaml file, for example
```yaml
price:
  item1: 100
  item2: 200
total: ${sum:${self:price.item1}:${self:price.item2}}
```


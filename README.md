# Yaml to Object Mapper

The mapper allows you to easily map yaml to PHP objects with validation and custom variables processing.
This mapper works with php 8 attributes which uses for describing mapping or/and validation rules for exact fields 

## Installation

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
use Diezz\YamlToObjectMapper\YamlConfigurable;

class Config implements YamlConfigurable {
    public string $name;
    public ConnectionSettings $connection
}

class ConnectionSettings implements YamlConfigurable {
    public string $host;
    public string $port;
    public string $username;
    public string $password;
}
```

so to map yml file to a config object we just need to 

```php
$config = ConfigMapper::make()->mapFromFile(Config::class, 'config.yml');
```

## Extended usage

### Usage of environment variables in a yml file
You can use environment variables in your yml file like this
```yaml
name: Test env variables
connection:
  host: localhost
  port: 3202
  username: $env::DB_USER
  password: $env::DB_PASSWORD
```
**Note:** if env variable can't be resolved the argument resolver will return `null`. 
In that case make sure you class field allowed null values or either exception would be thrown
### Validation
By default, the mapper checks that required fields are present in yml file.
There are multiple ways how the mapper defines which field is required:
1. The most obvious way is mark required field with `#[Required]` attribute.
2. If class field isn't marked with required attribute the mapper checks type hint of the field. It could be a php 7 
type hint or phpdoc comment. Nullable properties or properties initiated with default value are treated as not required 
or vice verse. 

```php
use Diezz\YamlToObjectMapper\Attributes\Required;
use Diezz\YamlToObjectMapper\YamlConfigurable;

class Model implements YamlConfigurable {
    /**
     * Required field 
     */
    #[Required]
    public $value0;
    
    /**
     * This field is required
     */
    public string $value1;

    /**
     * This field required as well based on type hint in the doc comment
     *
     * @var string
     */
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
```

### Variables processing

The mapper supports processing of variables which will be resolved during runtime.
Basic syntax for variables is `$varName::firstArgument::secondArgument`
Built-in variables:
- format
- substring 
- now
- self
- env

### Argument resolvers

### Default value resolving

Sometimes we want to treat yaml object name as a field of object

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

instead of writing `name` property every time and `columns` we may use this approach to avoid duplicates
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

and the `Table` model should be updated like this 

```php
class Table {
    #[DefaultValueResolver(resolver: DefaultValueResolver::PARENT_KEY)]
    public string $name;
    
    #[DefaultValueResolver(resolver: DefaultValueResolver::NESTED_LIST)]
    public array $columns;
}
```

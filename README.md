# Yaml to Object Mapper

The mapper allows you to easily map yaml to PHP objects with 
validation and custom variables processing

This mapper works with php 8 attributes which uses for describing mapping or/and validation rules for exact fields 

## Installation

## Simple usage
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

### Variables processing

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

instead of write `name` property every time and `columns` we may use this approach to avoid duplicates
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

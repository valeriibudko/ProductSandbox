### Description

Implementation **Mediator** with supporting CQRS (Command Query Responsibility Segregation), pipeline middleware: logging, validation, transactions...

### Deploy

```
composer install
cp phpunit.xml.dist phpunit.xml
```

### Run build
```
php bin/demo.php user@test.com 'Bob Donovan'

```

### Autotest

```
vendor/bin/phpunit
```
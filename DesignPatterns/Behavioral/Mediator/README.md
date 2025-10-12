### Description

Implementation **Mediator** with supporting CQRS (Command Query Responsibility Segregation), pipeline middleware: logging, validation, transactions...

### Run build

```
composer install
php bin/demo.php user@test.com 'Bob Donovan'
```

### Autotest

```
vendor/bin/phpunit src/Tests
```
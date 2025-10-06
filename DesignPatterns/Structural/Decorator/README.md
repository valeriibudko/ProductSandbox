### Description

Implementation **Decorator** for secure document storage with virus scanning, 
retries and audit logging layered around an S3 storage service.

### Run build

```
composer install
php bin/demo.php
```

### Autotest

```
vendor/phpunit/phpunit/phpunit src/Tests/Unit/
```
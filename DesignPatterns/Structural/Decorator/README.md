### Description

Implementation **Decorator** for secure document storage with virus scanning, 
retries and audit logging layered around an S3 storage service.

### Deploy
```
cp env.ini.dist env.ini
composer install
```

### Run build
```
php bin/demo.php
```

### Autotest
```
composer tests
```
### Description

Implementation **Abstract Factory** for implementation of notification components: email, SMS, templates. 
For different regulatory zones: EU, US. This allows you to switch the entire channel ecosystem with a single line, without rewriting the business logic code.

### Deploy
```
composer install
cp phpunit.xml.dist phpunit.xml
```

### Run build
First terminal:
```
php -S 127.0.0.1:8080 bin/mock-server.php

```
Second terminal:
```
php bin/demo.php EN
php bin/demo.php EU
php bin/demo.php
```

### Autotest
```
composer tests
```
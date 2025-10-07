### Description

Implementation **Facade**. The single CheckoutFacade point hides the payment gateway, 
warehouse reservation, delivery, invoicing, anti-fraud, and logging.

### Run build

```
composer install
php bin/demo.php
```

### Autotest

```
vendor/bin/phpunit src/Tests/Unit/CheckoutFacadeTest.php
```
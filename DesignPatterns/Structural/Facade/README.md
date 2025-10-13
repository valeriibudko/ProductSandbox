### Description

Implementation **Facade**. The single CheckoutFacade point hides the payment gateway, 
warehouse reservation, delivery, invoicing, anti-fraud, and logging.

### Deploy
```
composer install
```

### Run build
```
php bin/demo.php
```

### Autotest
```
vendor/bin/phpunit tests
```
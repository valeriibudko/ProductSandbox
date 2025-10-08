### Description

Implementation **Factory Method** for sending notifications via different channels: Email or SMS.

### Run build

```
composer install

php bin/demo.php email
php bin/demo.php email test@app.com SubjectTest BodyText

php bin/demo.php sms
php bin/demo.php sms 351912300111 SubjectTest BodyText
```

### Autotest

```
vendor/bin/phpunit src/Tests/Unit/
```
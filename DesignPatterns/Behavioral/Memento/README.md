### Description

Implementation **Memento** in document editor with versioning, undo/redo and storage limits. 
The structure is production-oriented: clear layers, interfaces, in-memory storage and file-based storage of snapshots.

### Deploy
```
composer install
cp phpunit.xml.dist phpunit.xml
```

### Run build
```
php bin/demo.php
```

### Autotest
```
vendor/bin/phpunit tests
```
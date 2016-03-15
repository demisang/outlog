# Outlog
===================
Outer logging system

Installation
---
Run
```code
php composer.phar require "demi/outlog" "~1.0"
```
or


Add to composer.json in your project
```json
{
	"require": {
  		"demi/outlog": "~1.0"
	}
}
```
then run command
```code
php composer.phar update
```

# Configurations
---

Log new exception
```php
$client = new Outlog($apiKey);
$client->basePath = '/var/www/site';

$client->notifyException($exception);
```
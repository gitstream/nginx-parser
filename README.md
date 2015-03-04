Nginx parser
============

```php
require __DIR__ . "/vendor/autoload.php";

use gitstream\parser\nginx\Parser;

$conf   = __DIR__ . '/tests/fixtures/nginx/nginx.conf';

$parser = new Parser();

print_r($parser->load($conf));
```

Tests
=====

```sh
phpunit --configuration ./phpunit.xml
```

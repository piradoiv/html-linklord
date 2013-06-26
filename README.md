LinkLord
========

[![Build Status](https://travis-ci.org/piradoiv/html-linklord.png?branch=master)](https://travis-ci.org/piradoiv/html-linklord)

Micro library to extract links from an HTML string.

How to install
--------------

The easies way to install the library is using [Composer](http://getcomposer.org/).

```javascript
{
  "require": {
    "piradoiv/linklord": "1.*"
  }
}
```

How to use it
-------------

```php
<?php
# Require Composer autoloader
require 'vendor/autoload.php';

$html = '<html><body><a href="http://www.example.com/">Example</a></body></html>';
$parser = new \PiradoIV\Html\LinkLord\Parser($html);
$links = $parser->getLinks();

foreach ($links as $node) {
  echo "{$node->anchorText}\n";
}
?>
```

Link nodes also includes other variables like _isNoFollow_ and _isImage_.
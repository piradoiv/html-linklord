LinkLord
========

[![Build Status](https://travis-ci.org/piradoiv/html-linklord.png?branch=master)](https://travis-ci.org/piradoiv/html-linklord)

PHP Micro library to extract links from an HTML string.

How to install
--------------

The easiest way to install the library is using [Composer](http://getcomposer.org/).

```javascript
{
  "require": {
    "piradoiv/linklord": "1.*"
  }
}
```

Please notice the library requires at least PHP 5.3 in order to work (tested on
__5.3.26__ and __5.4.16__)

How to use it
-------------

```php
# Require Composer autoloader
require 'vendor/autoload.php';

$html = '<html><body><a href="http://www.example.com/">Example</a></body></html>';
$parser = new \PiradoIV\Html\LinkLord\Parser($html);
$links = $parser->getLinks();

foreach ($links as $node) {
  echo "{$node->anchorText}\n";
}
```

Other features
--------------

__Follow/Nofollow__

LinkLord is able to know whether the links are followed or not.

```php
$node->isNoFollow;
```

__What about images?__

If the link has an image child, it will be detected.

```php
$node->isImage;
```

__Mentions__

Looking for mentions on the code?, LinkLord is able to recognise mentions on
the text, this means if there is a 'www.example.com' on the text __and__ is
not linked, this will count as a mention.

```php
$possibleMentions = array('www.example.com', 'www.anotherdomain.com');
$mentions = $parser->getMentions($possibleMentions);
```

Enjoy! :)


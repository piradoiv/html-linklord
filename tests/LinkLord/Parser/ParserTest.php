<?php

namespace PiradoIV\Html\LinkLord\Tests;

use PiradoIV\Html\LinkLord\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $html = '<p>Lorem ipsum <a href="#dolor" target="_blank">dolor</a> sit amet</p>';
    
    public function testParserCanExtractsLinksFromString()
    {
        $parser = new Parser($this->html);
        $links = $parser->getLinks();

        $this->assertNotEmpty($links);
    }
}

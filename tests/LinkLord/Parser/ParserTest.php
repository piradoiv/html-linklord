<?php

namespace PiradoIV\Html\LinkLord\Tests;

use PiradoIV\Html\LinkLord\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $html = <<<HTML
        <p>Lorem ipsum <a href="#dolor" target="_blank">dolor</a> sit amet</p>
        <p>Wop <a href="#image"><img src="wop.png" /></a></p>
HTML;
    
    public function testParserCanExtractsLinksFromString()
    {
        $parser = new Parser($this->html);
        $links = $parser->getLinks();

        $this->assertNotEmpty($links);
    }

    public function testParsedLinksHaveNofollowAttribute()
    {
        $parser = new Parser($this->html);
        $links = $parser->getLinks();

        foreach ($links as $link) {
            $this->assertNotNull($link->isNoFollow);
        }
    }

    public function testParsedLinksDetectsIfIsAnImage()
    {
        $parser = new Parser($this->html);
        $links = $parser->getLinks();

        $this->assertTrue($links[1]->isImage, true);
    }
}

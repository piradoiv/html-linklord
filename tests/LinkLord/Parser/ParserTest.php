<?php

namespace PiradoIV\Html\LinkLord\Tests;

use PiradoIV\Html\LinkLord\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $html = <<<HTML
        <p>Lorem ipsum <a href="#dolor" rel="_nofollow" target="_blank">dolor</a> sit amet</p>
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

        $this->assertTrue($links[0]->isNoFollow);
        $this->assertFalse($links[1]->isNoFollow);
    }

    public function testParsedLinksDetectsIfIsAnImage()
    {
        $parser = new Parser($this->html);
        $links = $parser->getLinks();

        $this->assertFalse($links[0]->isImage);
        $this->assertTrue($links[1]->isImage);
    }

    public function testParserWordCounterWorks()
    {
        $parser = new Parser('<p>Ola k ase</p>');
        $this->assertEquals(3, $parser->getWordsCounter());

        $parser = new Parser('<body>Ola k ase</body>');
        $this->assertEquals(0, $parser->getWordsCounter());

        $parser = new Parser('<html><body>Ola k ase. <p>Programando o ke ase?</p></body></html>');
        $this->assertEquals(4, $parser->getWordsCounter());
    }

    public function testParserProvidesAnchorText()
    {
        $parser = new Parser($this->html);
        $links = $parser->getLinks();

        $this->assertEquals('dolor', $links[0]->anchorText);
        $this->assertEquals('[IMAGE]', $links[1]->anchorText);
    }
}

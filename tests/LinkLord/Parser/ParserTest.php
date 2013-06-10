<?php

namespace PiradoIV\Html\LinkLord\Tests;

use \Symfony\Component\DomCrawler\Crawler;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testDomCrawlerIsAvailable()
    {
        $dom = new Crawler();
        $this->assertInstanceOf('\Symfony\Component\DomCrawler\Crawler', $dom);
    }
}

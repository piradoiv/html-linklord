<?php

namespace PiradoIV\Html\LinkLord;

use \Symfony\Component\DomCrawler\Crawler;

class Parser
{
    private $crawler;
    private $links;

    public function __construct($string = '')
    {
        $this->crawler = new Crawler($string);
        $this->links = array();
    }

    public function getLinks()
    {
        $this->crawler->filter('a')->each(
            function ($node, $i) {
                array_push($this->links, $node);
            }
        );

        return $this->links;
    }
}

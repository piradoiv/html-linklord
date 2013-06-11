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
                $node->isNoFollow = $this->isLinkNoFollow($node);
                $node->isImage    = $this->isLinkImage($node);

                array_push($this->links, $node);
            }
        );

        return $this->links;
    }

    private function isLinkNoFollow($node)
    {
        $rel = strtolower($node->attr('rel'));
        if ($rel == '_nofollow') {
            return true;
        } else {
            return false;
        }
    }

    private function isLinkImage($node)
    {
        $img = $node->filter('img');
        
        if (count($img) != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getWordsCounter()
    {
        return 0;
    }
}

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

    /**
     * Gets an array of links objects from the DOM
     * @return array An array of link nodes
     */
    public function getLinks()
    {
        $this->crawler->filter('a')->each(
            function ($node, $i) {
                $node->isNoFollow = $this->isLinkNoFollow($node);
                $node->isImage    = $this->isLinkImage($node);
                $node->anchorText = $this->getAnchorText($node);

                array_push($this->links, $node);
            }
        );

        return $this->links;
    }

    /**
     * Checks if a node link has the rel="_nofollow" attribute
     * @param  node  $node The link node to check
     * @return boolean     True if it's NoFollow, False otherwise
     */
    private function isLinkNoFollow($node)
    {
        $rel = strtolower($node->attr('rel'));
        if ($rel == '_nofollow') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if a link's node contains a children image
     * @param  node  $node The link node to check
     * @return boolean     True if it's an image, False otherwise
     */
    private function isLinkImage($node)
    {
        $img = $node->filter('img');

        if (count($img) != 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the anchor text of a link's node
     * @param  node $node The link node to use
     * @return string     The anchor text, [IMAGE] if it's an image
     */
    private function getAnchorText($node)
    {
        $anchorText = $node->text();
        if ($node->isImage) {
            $anchorText = "[IMAGE]";
        }

        return $anchorText;
    }

    /**
     * Gets an aprox. counter of words on the DOM, it currently just counts
     * the words inside "P" (Paragraph) tags
     * @return integer The counter of found words
     */
    public function getWordsCounter()
    {
        $counter = 0;

        foreach ($this->crawler->filter('p') as $p) {
            $text = $p->textContent;
            $counter += str_word_count($text);
        }

        return $counter;
    }

    public function getMentionsFromArray($possibleMentions = array())
    {
        $counter = 0;
        $text = $this->crawler->text();

        foreach ($possibleMentions as $mention) {
            $pattern = "/[ ,]{$mention}/i";
            $matches = array();
            $counter += preg_match_all($pattern, $text, $matches);
        }

        return $counter;
    }
}

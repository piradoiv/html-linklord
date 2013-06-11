<?php
/**
 * LinkLord - A tiny lib for extracting DOM's links
 *
 * @author Ricardo Cruz <piradoiv@gmail.com>
 * @copyright 2013 Ricardo Cruz
 * @link http://twitter.com/PiradoIV
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 1.0
 * @package PiradoIV\Html\LinkLord
 *
 * The MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
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

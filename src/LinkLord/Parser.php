<?php
/**
 * LinkLord - A tiny lib for extracting DOM's links
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   PiradoIV\Html\LinkLord
 * @author    Ricardo Cruz <piradoiv@gmail.com>
 * @copyright 2013 Ricardo Cruz
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @version   GIT: release/1.0
 * @link      http://twitter.com/PiradoIV
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

/**
 * Parser class
 *
 * @category  Library
 * @package   PiradoIV\Html\LinkLord
 * @author    Ricardo Cruz <piradoiv@gmail.com>
 * @copyright 2013 Ricardo Cruz
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @version   Release: 1.0
 * @link      http://twitter.com/PiradoIV
 */
class Parser
{
    public $crawler;
    public $links;
    public $mentions;

    /**
     * Constructor of the class
     * 
     * @param string $string The HTML string to be parsed
     */
    public function __construct($string = '')
    {
        $this->crawler = new Crawler($string);
    }

    /**
     * Gets an array of links objects from the DOM
     * 
     * @return array An array of link nodes
     */
    public function getLinks()
    {
        $context = &$this;

        if (!$context->links) {
            $context->links = array();
        }

        $this->crawler->filter('a')->each(
            function ($node, $i) use ($context) {
                $node->isNoFollow = $context->isLinkNoFollow($node);
                $node->isImage    = $context->isLinkImage($node);
                $node->anchorText = $context->getAnchorText($node);

                array_push($context->links, $node);
            }
        );

        return $this->links;
    }

    /**
     * Checks if a node link has the rel="_nofollow" attribute
     * 
     * @param node $node The link node to check
     * 
     * @return boolean     True if it's NoFollow, False otherwise
     */
    public function isLinkNoFollow($node)
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
     * 
     * @param node $node The link node to check
     * 
     * @return boolean True if it's an image, False otherwise
     */
    public function isLinkImage($node)
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
     * 
     * @param node $node The link node to use
     * 
     * @return string The anchor text, [IMAGE] if it's an image
     */
    public function getAnchorText($node)
    {
        $anchorText = '';

        try {
            $anchorText = $node->text();
        } catch (InvalidArgumentException $e) {
            // Leave text blank
        }

        if ($node->isImage) {
            $anchorText = "[IMAGE]";
        }

        return $anchorText;
    }

    /**
     * Gets an aprox. counter of words on the DOM, it currently just counts
     * the words inside "P" (Paragraph) tags
     * 
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

    /**
     * Returns the counter of mentions
     * 
     * @param array $possibleMentions An array of possible mentions
     * 
     * @return array An array of found mentions
     */
    public function getMentions($possibleMentions = array())
    {
        // First of all, we need to fetch links to avoid some anchor text
        // on links beign detected as a mention
        if (!$this->links) {
            $this->getLinks();
        }

        $this->mentions = array();

        // Fetchs only the text from the HTML
        $text = '';
        try {
            $text = $this->crawler->text();
        } catch (InvalidArgumentException $e) {
            // Leave text blank
        }

        // Search mentions on the text
        foreach ($possibleMentions as $mention) {
            $mention = str_replace('/', '\/', $mention);
            $pattern = "/{$mention}/i";
            $matches = array();
            preg_match_all($pattern, $text, $matches);

            foreach ($matches[0] as $m) {
                array_push($this->mentions, $m);
            }
        }

        // Remove mentions found in links
        foreach ($this->mentions as $index => $mention) {
            foreach ($this->links as $link) {
                $anchorText = '';
                try {
                    $anchorText = $link->text();
                } catch(InvalidArgumentException $e) {
                    continue;
                }
                
                $pattern = str_replace('/', '\/', $mention);
                $pattern = "/{$pattern}/i";
                if (preg_match($pattern, $anchorText)) {
                    unset($this->mentions[$index]);
                }
            }
        }

        return $this->mentions;
    }
}

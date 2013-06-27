<?php
/**
 * LinkLord - A tiny lib for extracting DOM's links
 *
 * PHP version 5.3
 *
 * @category  Library
 * @package   PiradoIV\Html\LinkLord\Tests
 * @author    Ricardo Cruz <piradoiv@gmail.com>
 * @copyright 2013 Ricardo Cruz
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @version   GIT: release/1.1.1
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

    public function testParserIsAbleToFindMentions()
    {
        $html = <<<HTML
        <p>Lorem ipsum <a href="http://www.piradoiv.com/" rel="_nofollow" target="_blank">Pirado IV website</a> sit amet</p>
        <p>Check it at www.piradoiv.com <a href="#image"><img src="wop.png" /></a></p>
        <p>One, two, three, piradoiv.com was here!</p>
HTML;

        $parser = new Parser($html);
        $possibleMentions = array(
            'piradoiv.com',
            'Pirado IV Website'
        );

        $result = $parser->getMentions($possibleMentions);

        $this->assertEquals(2, count($result));
    }

    public function testExceptionOnEmptyText()
    {
        $html = '';
        $parser = new Parser($html);
        $possibleMentions = array('testing');

        $result = $parser->getMentions($possibleMentions);
        
        $this->assertEquals(array(), $result);
    }

    public function testNofollowVariants()
    {
        $html = <<<HTML
        <p>Lorem ipsum <a href="http://www.piradoiv.com/" rel="_nofollow" target="_blank">Pirado IV website</a> sit amet</p>
        <p>Lorem ipsum <a href="http://www.piradoiv.com/" rel="nofollow" target="_blank">Pirado IV website</a> sit amet</p>
        <p>Lorem ipsum <a href="http://www.piradoiv.com/" target="_blank">Pirado IV website</a> sit amet</p>
HTML;

        $parser = new Parser($html);
        $links = $parser->getLinks();

        $this->assertTrue($links[0]->isNoFollow, '_nofollow should be detected');
        $this->assertTrue($links[1]->isNoFollow, 'nofollow should be detected');
        $this->assertFalse($links[2]->isNoFollow, "there isn't nofollow here");
        
    }
}

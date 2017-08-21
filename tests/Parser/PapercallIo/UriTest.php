<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
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
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     07.07.2017
 * @link      http://github.com/heiglandreas/callingallpapers_cli
 */

namespace CallingallpapersTest\Parser\PapercallIo;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Parser\PapercallIo\Uri;
use IvoPetkov\HTML5DOMDocument as DOMDocument;
use DOMXPath;

class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testThatNameIsParsedCorrectlyFromNode()
    {
        $parser = new Uri();

        $dom = new DOMDocument();
        $cfp = new Cfp();

        $dom->loadHTMLFile(__DIR__ . '/_assets/conf2.html', LIBXML_HTML_NODEFDTD & LIBXML_HTML_NOIMPLIED);

        $dom1 = new DOMDocument();
        $dom1->loadHTMLFile(__DIR__ . '/_assets/index.html', LIBXML_HTML_NODEFDTD & LIBXML_HTML_NOIMPLIED);
        $xpath = new DOMXPath($dom1);
        $nodes = $xpath->query("//div[contains(@class,'main')]/div[@class='container'][2]//div[@class='box']");
        $node = $nodes[0];

        $newcfp = $parser->parse($dom, $node, $cfp);

        $this->assertSame($newcfp, $cfp);
        $this->assertEquals('https://papercall.io/cfps/218/submissions/new', $newcfp->uri);
    }
}

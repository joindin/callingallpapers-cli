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
 * @since     29.07.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace Callingallpapers\Parser\PapercallIo;

use Callingallpapers\Parser\ParserInterface;
use Callingallpapers\Writer\WriterInterface;
use IvoPetkov\HTML5DOMDocument as DOMDocument;
use DOMXPath;

class PapercallIoParser implements ParserInterface
{
    const SOURCE = 'papercallio';

    private $uri = 'https://www.papercall.io/events?open-cfps=true&page=%1$s';

    private $parser;

    public function __construct(EventParser $parser)
    {
        $this->parser = $parser;
    }

    public function setStartUrl(string $uri)
    {
        $this->uri = $uri;
    }

    public function parse(WriterInterface $writer)
    {
        $i = 0;

        $pages = null;

        do {
            $dom = new DOMDocument('1.0', 'UTF-8');
            libxml_use_internal_errors(true);
            $uri = sprintf($this->uri, $i+1);
            $dom->loadHTMLFile($uri);
            libxml_use_internal_errors(false);
            $dom->preserveWhiteSpace = false;
            $xpath = new DOMXPath($dom);

            if (null === $pages) {
                $p = $xpath->query("//ul[contains(@class,'pagination')]/li");
                $pages = $p->length - 2;
            }

            $nodes = $xpath->query("//div[contains(@class,'main')]/div[@class='container'][last()]//div[contains(@class,'event-list-detail')]");
            if ($nodes->length < 1) {
                error_log('No subitems found');
                continue;
            }

            /** @var DOMNode $node */
            foreach ($nodes as $node) {
                try {
                    $writer->write($this->parser->parseEvent($node), self::SOURCE);
                } catch (\Exception $e) {
                    // Do nothing
                }
            }
        } while (++$i < $pages);

        return true;
    }
}

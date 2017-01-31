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

namespace Callingallpapers\Parser;

use Callingallpapers\Entity\CfpList;
use Callingallpapers\Parser\PapercallIo\Entry;
use Callingallpapers\Writer\WriterInterface;

class PapercallIoParser implements ParserInterface
{

    protected $tzService;

    public function __construct(TimezoneService $tzService)
    {
        $this->tzService = $tzService;
    }

    public function parse(WriterInterface $writer)
    {
        $uri = 'http://papercall.io/cfps';
        $i = 0;

        $pages = 0;

        do {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadHTMLFile($uri . '?page=' . ($i+1));
            $dom->preserveWhiteSpace = false;

            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query("//div[@class='box__content']/ul[contains(@class, 'pagination')]/li/a");
            if ($nodes->length < 1) {
                continue;
            }
            $pages = $nodes[($nodes->length - 2)]->textContent;

            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query("//div[@class='main']/div[@class='container']/div[@class='box']");

            $cfp = new Cfp();
            $client = new Client([
                'headers'=> [
                    'User-Agent' => 'callingallpapers.com - Location to lat/lon-translation - For infos write to andreas@heigl.org',
                ],
            ]);

            $papercallIoEntryParser = new Entry($cfp, $client, $this->tzService);

            foreach ($nodes as $node) {
                try {
                    /** @var \DOMNode $node */
                    $links = $xpath->query('.//a[text()="Call for speakers"]',
                        $node);
                    if ($links->length < 1) {
                        continue;
                    }

                    $eventPageUrl = $links->item(0)->attributes->getNamedItem('href')->textContent;
                    error_log($eventPageUrl);
                    if (! $eventPageUrl) {
                        continue;
                    }
                    $writer->write($papercallIoEntryParser->parse($eventPageUrl), 'papercall.io');
                } catch (\Exception $e) {
                    error_log($e->getMEssage());
                }
            }
        } while (++$i < $pages);

        return true;
    }
}

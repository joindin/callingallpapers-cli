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
 * @since     05.07.2017
 * @link      http://github.com/heiglandreas/callingallpapers_cli
 */

namespace Callingallpapers\Parser\PapercallIo;

use Callingallpapers\Parser\EventDetailParserInterface;
use IvoPetkov\HTML5DOMDocument as DOMDocument;
use DOMNode;
use DOMXpath;
use Callingallpapers\Entity\Cfp;
use Callingallpapers\Parser\EventParserInterface;

class EventParser implements EventParserInterface
{
    private $eventParser;

    public function __construct(EventDetailParserInterface $eventParser)
    {
        $this->eventParser = $eventParser;
    }

    public function parseEvent(DOMNode $eventNode) : Cfp
    {
        $xpath = new DOMXPath($eventNode->ownerDocument);
        $anchor = $xpath->query('.//div[@class="pack__item"]/a', $eventNode);
        /** @var \DOMNode $node */
        $eventPageUrl = $anchor->item(0)->attributes->getNamedItem('href')->textContent;

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTMLFile($eventPageUrl);

        return $this->eventParser->parse($document, $eventNode, new CfP());
    }
}

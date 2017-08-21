<?php
/**
 * Copyright (c) 2015-2015 Andreas Heigl<andreas@heigl.org>
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
 * @copyright 2015-2015 Andreas Heigl/callingallpapers.com
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     06.03.2012
 * @link      http://github.com/joindin/callingallpapers
 */
namespace Callingallpapers\Parser\Lanyrd;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Parser\EventDetailParserInterface;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMNode;
use DOMXPath;

class EventEndDate implements EventDetailParserInterface
{
    protected $timezone;

    public function __construct($timezone = 'UTC')
    {
        $this->timezone = new DateTimezone($timezone);
    }

    public function parse(DOMDocument $dom, DOMNode $xpath, Cfp $cfp) : Cfp
    {
        $endDate = $xpath->query("//div[contains(@class, 'vevent')]/*/abbr[contains(@class, 'dtend')]"); ///a/abbr[class='dtstart']
        if (! $endDate || $endDate->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have an end date');
        }

        $endDate = $endDate->item(0)->attributes->getNamedItem('title')->textContent;

        return new \DateTime($endDate, $this->timezone);

        $xpath = new DOMXPath($dom);
        $closingDate = $xpath->query(".//time/@datetime", $node);
        if (! $closingDate || $closingDate->length == 0) {
            throw new InvalidArgumentException('The CfP does not seem to have a closing date');
        }

        $closingDate = $closingDate->item(0)->textContent;

        $cfp->dateEnd = new DateTimeImmutable($closingDate);
        $cfp->dateEnd = $cfp->dateEnd->setTimezone($this->timezone);

        return $cfp;


    }
}

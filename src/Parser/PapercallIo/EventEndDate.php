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
namespace Callingallpapers\Parser\PapercallIo;

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

    public function parse(DOMDocument $dom, DOMNode $node, Cfp $cfp) : Cfp
    {
        $timezone = $this->timezone;
        if ($cfp->timezone) {
            $timezone = $cfp->timezone;
        }


        $xpath = new DOMXPath($dom);
        $titlePath = $xpath->query("//h1[contains(@class, 'subheader__subtitle')]");

        if (! $titlePath || $titlePath->length == 0) {
            return $cfp;
        }

        $locationTimeString = trim($titlePath->item(0)->textContent);
        $locationTime = explode(' - ', $locationTimeString);

        if (! isset($locationTime[1])) {
            return $cfp;
        }

        $dates = explode(',', $locationTime[1]);
        if (count($dates) % 2  !== 0) {
            return $cfp;
        }

        $datestring = $dates[0] . ', ' . $dates[1];

        if (count($dates) >= 4) {
            $datestring = $dates[2] . ', ' . $dates[3];
        }

        $endDate = new DateTimeImmutable($datestring . ' 00:00:00', $timezone );
        $cfp->eventEndDate = $endDate;

        return $cfp;
    }
}

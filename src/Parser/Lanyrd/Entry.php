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
use Callingallpapers\Service\TimezoneService;
use GuzzleHttp\Client;

class Entry
{
    /** @var  Cfp */
    protected $cfp;

    /** @var Client */
    protected $client;

    public function __construct(Cfp $cfp, Client $client, TimezoneService $timezoneService)
    {
        $this->cfp = $cfp;
        $this->client = $client;
        $this->tzService = $timezoneService;
    }

    public function parse($uri)
    {
        $cfp = clone($this->cfp);
        try {
            $dom = new \DOMDocument('1.0', 'UTF-8');

            $content = file_Get_contents($uri);
            $content = mb_convert_encoding($content, 'UTF-8');
            $dom->loadHTML('<?xml version="1.0" charset="UTF-8" ?>' . $content);
            $dom->preserveWhiteSpace = false;

            $timezone = 'UTC';
            $xpath = new \DOMXPath($dom);

            $eventLocation = new Location();
            $cfp->location = $eventLocation->parse($dom, $xpath);

            try {
                $location = $this->getLatLonForLocation($cfp->location);
                $cfp->latitude = $location[0];
                $cfp->longitude = $location[1];
                $timezone = $this->tzService->getTimezoneForLocation($location[0], $location[1]);
            } catch (\UnexpectedValueException $e) {
                error_log($e->getMessage());
            }
            $cfp->timezone = $timezone;


            $closingDateParser = new ClosingDate($timezone);
            $cfp->dateEnd = $closingDateParser->parse($dom, $xpath);

            $eventPageDom = $this->getEventPage($xpath);
            $eventXpath = new \DOMXPath(($eventPageDom));

            $descriptionParser = new Description();
            $cfp->description = $descriptionParser->parse($dom, $xpath);

            $openingDateParser = new OpeningDate($timezone);
            try {
                $cfp->dateStart = $openingDateParser->parse($dom, $xpath);
            } catch (\Exception $e) {
            }

            $cfpUriParser = new Uri();
            $cfp->uri = $cfpUriParser->parse($dom, $xpath);

            $confNameParser = new EventName();
            $cfp->conferenceName = $confNameParser->parse($dom, $xpath);

            $confUriParser = new EventUri();
            $cfp->conferenceUri  = $confUriParser->parse($eventPageDom, $eventXpath);

            $eventStartDate = new EventStartDate($timezone);
            $cfp->eventStartDate = $eventStartDate->parse($dom, $xpath);

            try {
                $eventEndDate      = new EventEndDate($timezone);
                $cfp->eventEndDate = $eventEndDate->parse($dom, $xpath);
            } catch (\InvalidArgumentException $e) {
                $cfp->eventEndDate = $cfp->eventStartDate;
            }

            $eventLocation = new Location();
            $cfp->location = $eventLocation->parse($dom, $xpath);

            try {
                $location = $this->getLatLonForLocation($cfp->location);
                $cfp->latitude = $location[0];
                $cfp->longitude = $location[1];
            } catch (\UnexpectedValueException $e) {
                error_log($e->getMessage());
            }

            try {
                $tags = new Tags();
                $cfp->tags = $tags->parse($eventPageDom, $eventXpath);
            } catch (\InvalidArgumentException $e) {
                $cfp->tags = [];
            }

            return $cfp;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getLatLonForLocation($location)
    {
        $result = $this->client->get(sprintf(
            'https://nominatim.openstreetmap.org/search?q=%1$s&format=json',
            urlencode($location)
        ));

        $locations = json_decode($result->getBody()->getContents());

        if (empty($locations)) {
            return [0, 0];
        }
        $location = $locations[0];

        return [$location->lat, $location->lon];
    }

    public function getEventPage($xpath)
    {
        $confPath = $xpath->query("//h3/a[contains(@class, 'summary')]");

        if (! $confPath || $confPath->length == 0) {
            throw new \InvalidArgumentException('We can\'t find an EventPage');
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');

        $content = file_get_contents('https://lanyrd.com' . $confPath->item(0)->attributes->getNamedItem('href')->textContent);
        $content = mb_convert_encoding($content, 'UTF-8');
        $dom->loadHTML('<?xml version="1.0" charset="UTF-8" ?>' . $content);
        $dom->preserveWhiteSpace = false;

        return $dom;
    }
}

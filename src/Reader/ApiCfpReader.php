<?php
/**
 * Copyright (c) 2015-2016 Andreas Heigl<andreas@heigl.org>
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
 * @copyright 2015-2016 Andreas Heigl/callingallpapers.com
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     01.12.2015
 * @link      http://github.com/heiglandreas/callingallpapers-cli
 */
namespace Callingallpapers\Reader;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Entity\CfpList;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use GuzzleHttp\Client;

class ApiCfpReader
{
    protected $baseUri;

    protected $bearerToken;

    protected $client;

    public function __construct($baseUri, $bearerToken, $client = null)
    {
        $this->baseUri = str_Replace('/cfp', '', $baseUri);

        $this->bearerToken = $bearerToken;
        if (null === $client) {
            $client = new Client([
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);
        }
        $this->client = $client;
    }

    public function getCfpsEndingWithinInterval(DateInterval $interval, DateTimeInterface|null $date = null): CfpList
    {
        if (null === $date) {
            $date = new DateTimeImmutable();
        }

        $endDate = $date->add($interval);

        $list = new CfpList();

        try {
            $result = $this->client->get(sprintf(
                '%s/search?date_cfp_end[]=%s&date_cfp_end_compare[]=%s&date_cfp_end[]=%s&date_cfp_end_compare[]=%s',
                $this->baseUri,
                urlencode($date->setTimezone(new DateTimeZone('UTC'))->format('c')),
                urlencode('>'),
                urlencode($endDate->setTimezone(new DateTimeZone('UTC'))->format('c')),
                urlencode('<')
            ), [
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);

            $items = \GuzzleHttp\json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return $list;
        }

        if (! $items) {
            return $list;
        }

        foreach ($items['cfps'] as $item) {
            $cfp = new Cfp();
            $cfp->conferenceName = $item['name'];
            $cfp->conferenceUri  = $item['eventUri'];
            $cfp->dateEnd        = new DateTimeImmutable($item['dateCfpEnd']);
            $cfp->dateStart      = new DateTimeImmutable($item['dateCfpStart']);
            $cfp->eventEndDate   = new DateTimeImmutable($item['dateEventEnd']);
            $cfp->eventStartDate = new DateTimeImmutable($item['dateEventStart']);
            $cfp->description    = $item['description'];
            $cfp->location       = $item['location'];
            $cfp->latitude       = $item['latitude'];
            $cfp->longitude      = $item['longitude'];
            $cfp->iconUri        = $item['iconUri'];
            $cfp->uri            = $item['uri'];
            $cfp->tags           = array_filter($item['tags'], function ($item) {
                return (bool) $item;
            });
            $cfp->timezone       = $item['timezone'];

            $tz = new DateTimeZone($cfp->timezone);

            $cfp->dateEnd->setTimezone($tz);
            $cfp->dateStart->setTimezone($tz);
            $cfp->eventEndDate->setTimezone($tz);
            $cfp->eventStartDate->setTimezone($tz);

            $list->append($cfp);
        }

        return $list;
    }
}

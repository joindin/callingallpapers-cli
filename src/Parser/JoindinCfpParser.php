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
namespace Callingallpapers\Parser;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Entity\CfpList;
use GuzzleHttp\Client;

class JoindinCfpParser implements ParserInterface
{

    public function parse()
    {
        $uri = 'http://api.joind.in/v2.1/events?filter=cfp&verbose=yes';

        $client = new Client();
        $content = $client->get($uri)->getBody();

        $content = json_decode($content, true);

        $contents = new CfpList();

        foreach($content['events'] as $event) {
            if (! $event['cfp_url']) {
                continue;
            }
            $info = new Cfp();
            $info->conferenceName = $event['name'];
            $info->conferenceUri = $event['href'];
            $info->eventEndDate = new \DateTimeImmutable($event['end_date']);
            $info->eventStartDate = new \DateTimeImmutable($event['start_date']);
            $info->dateEnd = new \DateTimeImmutable($event['cfp_end_date']);
            $info->dateStart = new \DateTimeImmutable($event['cfp_start_date']);
            $info->description = $event['description'];
            $info->geolocation = $event['location'];
            $info->latitude = $event['latitude'];
            $info->longitude = $event['longitude'];
            $info->tags = $event['tags'];
            $info->uri = $event['cfp_url'];
            $info->timezone = $event['tz_continent'] . '/' . $event['tz_place'];

            $contents->append($info);
        }

        return $contents;
    }


}
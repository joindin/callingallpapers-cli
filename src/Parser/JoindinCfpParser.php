<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
            var_Dump($event);
            if (! $event['cfp_url']) {
                continue;
            }
            $info = new Cfp();
            $info->conferenceName = $event['name'];
            $info->conferenceUri = $event['humane_website_uri'];
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

            $contents->append($info);
        }

        return $contents;
    }


}
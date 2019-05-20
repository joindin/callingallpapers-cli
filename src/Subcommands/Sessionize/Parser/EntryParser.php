<?php

declare(strict_types = 1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */
namespace Callingallpapers\Subcommands\Sessionize\Parser;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Service\ServiceContainer;
use Callingallpapers\Service\TimezoneService;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;

class EntryParser
{
    private $cfp;
    
    private $timezoneService;

    private $geolocationService;

    public function __construct(Cfp $cfp, ServiceContainer $container)
    {
        $this->cfp = $cfp;
        $this->timezoneService = $container->getTimezoneService();
        $this->geolocationService = $container->getGeolocationService();
    }

    public function parse($uri)
    {
        $cfp = clone($this->cfp);
        try {
            $dom = new DOMDocument('1.0', 'UTF-8');

            $content = file_get_contents($uri);
            $content = mb_convert_encoding($content, 'UTF-8');
            $dom->loadXML(
                '<?xml version="1.0" encoding="UTF-8" ?>' . $content,
                LIBXML_NOBLANKS ^ LIBXML_NOERROR ^ LIBXML_NOENT
            );
            $dom->preserveWhiteSpace = false;

            $xpath = new DOMXPath($dom);

            $eventLocation = new Location();
            $cfp->location = $eventLocation->parse($dom, $xpath);

            try {
                $location = $this->geolocationService->getLocationForAddress($cfp->location);
                $cfp->latitude = $location[0];
                $cfp->longitude = $location[1];
                $timezone = $this->timezoneService->getTimezoneForLocation($location[0], $location[1]);
            } catch (\UnexpectedValueException $e) {
                error_log($e->getMessage());
            }
            $cfp->timezone = $timezone;

            $timezone = new DateTimeZone($cfp->timezone);

            $closingDateParser = new ClosingDate($timezone);
            $cfp->dateEnd = $closingDateParser->parse($dom, $xpath);

            $descriptionParser = new Description();
            $cfp->description = $descriptionParser->parse($dom, $xpath);

            $openingDateParser = new OpeningDate($timezone);
            try {
                $cfp->dateStart = $openingDateParser->parse($dom, $xpath);
            } catch (Exception $e) {
            }

            $cfp->uri = $uri;

            $confNameParser = new EventName();
            $cfp->conferenceName = $confNameParser->parse($dom, $xpath);

            $confUriParser = new EventUri();
            $cfp->conferenceUri  = $confUriParser->parse($dom, $xpath);

            $eventStartDate = new EventStartDate($timezone);
            $cfp->eventStartDate = $eventStartDate->parse($dom, $xpath);

            try {
                $eventEndDate      = new EventEndDate($timezone);
                $cfp->eventEndDate = $eventEndDate->parse($dom, $xpath);
            } catch (InvalidArgumentException $e) {
                $cfp->eventEndDate = $cfp->eventStartDate;
            }

            $iconUri = new IconUri($uri);
            $cfp->iconUri = $iconUri->parse($dom, $xpath);

            return $cfp;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

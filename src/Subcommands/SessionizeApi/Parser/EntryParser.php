<?php

declare(strict_types = 1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */
namespace Callingallpapers\Subcommands\SessionizeApi\Parser;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Service\ServiceContainer;
use Callingallpapers\Service\TimezoneService;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;
use Throwable;

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

    /**
     * @param array{
     *      eventId: int,
     *      name: string,
     *      organizer: string,
     *      website: string,
     *      cfpLink: string,
     *      isTest: boolean,
     *      isOnline: boolean,
     *      isUserGroup: boolean,
     *      expensesCovered: array{
     *          conferenceFee: boolean,
     *          accommodation: boolean,
     *          travel: boolean
     *      },
     *      eventDates: array{
     *          start: datestring,
     *          end: datestring
     *      },
     *      cfpDates: array{
     *          startUtc: datetimestringZ,
     *          endUtc: datetimestringZ,
     *          start: datetimestring,
     *          end: datetimestring
     *      },
     *      timezone: array{
     *          iana: string,
     *          windows: string
     *      },
     *      location: array{
     *          full: string,
     *          city: string,
     *          state: string,
     *          country: string,
     *          coordinates: latlonstring
     *      },
     *      links: array{
     *          twitter: string,
     *          linkedIn: null,
     *          facebook: string,
     *          instagram: string,
     *      }
     *  } $event
     * @return Cfp
     * @throws Exception
     */
    public function parse(array $event): Cfp
    {
        $cfp = new Cfp();

        try {
            $cfp->location = $event['location']['full']??'';
            if ($event['location']['coordinates']??null !== null) {
                $location = explode(',', $event['location']['coordinates']);
                $cfp->latitude = $location[0];
                $cfp->longitude = $location[1];
            }
            $cfp->timezone = $event['timezone']['iana'];

            $timezone = new DateTimeZone($cfp->timezone);

            $cfp->dateEnd = new DateTimeImmutable($event['cfpDates']['end'], $timezone);
            $cfp->dateStart = new DateTimeImmutable($event['cfpDates']['start'], $timezone);

            $cfp->uri = $event['cfpLink'];
            $cfp->conferenceName = $event['name'];

            $cfp->conferenceUri  = $event['website'];

            $cfp->eventEndDate = new DateTimeImmutable($event['eventDates']['end'], $timezone);
            $cfp->eventStartDate = new DateTimeImmutable($event['eventDates']['start'], $timezone);

            return $cfp;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

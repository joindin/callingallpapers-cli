<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\Parser\ConfsTech;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Service\GeolocationService;
use Callingallpapers\Service\TimezoneService;
use DateTimeImmutable;
use DateTimeZone;

class ConferenceParser
{
    private $geolocation;

    private $timezone;

    public function __construct(
        GeolocationService $geolocation,
        TimezoneService $timezone
    ) {
        $this->geolocation = $geolocation;
        $this->timezone    = $timezone;
    }

    public function __invoke(array $conference) : Cfp
    {
        $cfp = new Cfp();
        if (isset($conference['city'])) {
            $cfp->location = $conference['city'];
        }

        if (isset($conference['country'])) {
            $geolocation = $this->geolocation->getLocationForAddress(
                $conference['country'] . ', ' . $cfp->location
            );
        }

        $cfp->latitude = $geolocation->getLatitude();
        $cfp->longitude = $geolocation->getLongitude();

        $cfp->timezone = $this->timezone->getTimezoneForLocation(
            $cfp->latitude,
            $cfp->longitude
        );

        $cfp->conferenceName = $conference['name'];
        $cfp->eventStartDate = new DateTimeImmutable(
            $conference['startDate'] . ' 08:00:00',
            new DateTimeZone($cfp->timezone)
        );
        if (! isset($conference['endDate'])) {
            $conference['endDate'] = $conference['startDate'];
        }
        $cfp->eventEndDate = new DateTimeImmutable(
            $conference['endDate'] . ' 17:00:00',
            new DateTimeZone($cfp->timezone)
        );

        $cfp->dateEnd = new DateTimeImmutable(
            $conference['cfpEndDate'] . ' 23:59:59',
            new DateTimeZone($cfp->timezone)
        );

        $cfp->conferenceUri = $conference['url'];
        $cfp->uri = $conference['url'];
        if (isset($conference['cfpUrl'])) {
            $cfp->uri = $conference['cfpUrl'];
        }

        return $cfp;
    }
}

<?php

declare(strict_types=1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Callingallpapers\Service;

use GuzzleHttp\Client;

class ServiceContainer
{
    private $timezone;

    private $geolocation;

    private $client;

    public function __construct(
        TimezoneService $timezone,
        GeolocationService $geolocation,
        Client $client
    ) {
        $this->timezone = $timezone;
        $this->geolocation = $geolocation;
        $this->client = $client;
    }

    public function getTimezoneService() : TimezoneService
    {
        return $this->timezone;
    }

    public function getGeolocationService() : GeolocationService
    {
        return $this->geolocation;
    }

    public function getClient() : Client
    {
        return $this->client;
    }
}

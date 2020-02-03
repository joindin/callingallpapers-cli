<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace CallingallpapersTest\Cli\Parser\ConfsTech;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Entity\Geolocation;
use Callingallpapers\Parser\ConfsTech\ConferenceParser;
use Callingallpapers\Service\GeolocationService;
use Callingallpapers\Service\TimezoneService;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Mockery as M;

class ConferenceParserTest extends TestCase
{
    /** @var TimezoneService */
    private $timezone;

    /** @var GeolocationService */
    private $geolocation;

    /** @var ConferenceParser */
    private $conferenceParser;

    public function setup(): void
    {
        $this->timezone = M::mock(TimezoneService::class);
        $this->geolocation = M::mock(GeolocationService::class);

        $this->conferenceParser = new ConferenceParser(
            $this->geolocation,
            $this->timezone
        );

        parent::setup();
    }

    /**
     * @throws \Exception
     * @covers \Callingallpapers\Parser\ConfsTech\ConferenceParser::__invoke
     */
    public function testInvokation()
    {
        $array = [
            "name"       => "PHPBenelux Conference",
            "url"        => "https://conference.phpbenelux.eu/2018",
            "startDate"  => "2018-01-26",
            "endDate"    => "2018-01-27",
            "city"       => "Antwerp",
            "country"    => "Belgium",
            "twitter"    => "@phpbenelux",
            "cfpEndDate" => "2018-04-15",
            "cfpUrl"     => "https://cfp.southeastphp.com"
        ];

        $this->timezone->shouldReceive('getTimezoneForLocation')->andReturn('Europe/Berlin');
        $this->geolocation->shouldReceive('getLocationForAddress')->andReturn(new Geolocation(20, 10));

        $cfp = ($this->conferenceParser)($array);

        $startDate = new DateTimeImmutable(
            $array['startDate'] . ' 08:00:00',
            new DateTimeZone('Europe/Berlin')
        );

        $endDate = new DateTimeImmutable(
            $array['endDate'] . ' 17:00:00',
            new DateTimeZone('Europe/Berlin')
        );

        $cfpEndDate = new DateTimeImmutable(
            $array['cfpEndDate'] . ' 23:59:59',
            new DateTimeZone('Europe/Berlin')
        );

        self::assertInstanceOf(Cfp::class, $cfp);
        self::assertSame($array['name'], $cfp->conferenceName);
        self::assertSame($array['city'], $cfp->location);
        self::assertSame(20.0, $cfp->latitude);
        self::assertSame(10.0, $cfp->longitude);
        self::assertSame('Europe/Berlin', $cfp->timezone);
        self::assertEquals($startDate, $cfp->eventStartDate);
        self::assertEquals($endDate, $cfp->eventEndDate);
        self::assertEquals($cfpEndDate, $cfp->dateEnd);
        self::assertSame($array['url'], $cfp->conferenceUri);
        self::assertSame($array['cfpUrl'], $cfp->uri);
    }

    /**
     * @covers \Callingallpapers\Parser\ConfsTech\ConferenceParser::__construct
     */
    public function testConstruction()
    {
        self::assertInstanceOf(ConferenceParser::class, $this->conferenceParser);
    }
}

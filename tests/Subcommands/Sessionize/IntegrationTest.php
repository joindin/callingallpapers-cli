<?php
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Cli\Subcommands\Sessionize;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Entity\Geolocation;
use Callingallpapers\Service\GeolocationService;
use Callingallpapers\Service\ServiceContainer;
use Callingallpapers\Service\TimezoneService;
use Callingallpapers\Subcommands\Sessionize\Parser\EntryParser;
use GuzzleHttp\Client;
use Mockery as M;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    public function testParsingOfAvailableAssets()
    {
        //$this->markTestSkipped('Needs to be implemented correctly');
        $cfp = new Cfp();

        /** @var TimezoneService|M\Mock $timezoneService */
        $timezoneService = M::mock(TimezoneService::class);
        $timezoneService->shouldReceive('getTimezoneForLocation')->andReturn('Europe/Paris');

        /** @var GeolocationService|M\Mock $geolocationService */
        $geolocationService = M::mock(GeolocationService::class);
        $geolocationService->shouldReceive('getLocationForAddress')->andReturn(new Geolocation(12.23, 23.34));

        $serviceContainer = new ServiceContainer(
            $timezoneService,
            $geolocationService,
            M::mock(Client::class)
        );
        $parser = new EntryParser($cfp, $serviceContainer);

        $result = $parser->parse('https://sessionize.com/kanddinsky-2018/');

        self::assertEquals('KanDDDinsky', $result->conferenceName);
        self::assertContains('The KanDDDinsky Team', $result->description);
    }
}

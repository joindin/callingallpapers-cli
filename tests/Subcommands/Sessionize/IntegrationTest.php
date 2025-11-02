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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EntryParser::class)]
#[UsesClass(Cfp::class)]
class IntegrationTest extends TestCase
{
    public function testParsingOfAvailableAssets()
    {
        $cfp = new Cfp();

        /** @var TimezoneService */
        $timezoneService = $this->createMock(TimezoneService::class);
        $timezoneService->method('getTimezoneForLocation')->willReturn('Europe/Paris');

        /** @var GeolocationService $geolocationService */
        $geolocationService = $this->createMock(GeolocationService::class);
        $geolocationService->method('getLocationForAddress')->willReturn(new Geolocation(12.23, 23.34));

        $serviceContainer = new ServiceContainer(
            $timezoneService,
            $geolocationService,
            M::mock(Client::class)
        );
        $parser = new EntryParser($cfp, $serviceContainer);

        $result = $parser->parse('https://sessionize.com/kanddinsky-2018/');

        self::assertEquals('KanDDDinsky', $result->conferenceName);
        self::assertStringContainsString('The KanDDDinsky Team', $result->description);
    }
}

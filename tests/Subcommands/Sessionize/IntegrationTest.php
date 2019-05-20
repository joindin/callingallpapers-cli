<?php
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Cli\Subcommands\Sessionize;

use Callingallpapers\Entity\Cfp;
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
        $serviceContainer = new ServiceContainer(
            M::mock(TimezoneService::class),
            M::mock(GeolocationService::class),
            M::mock(Client::class)
        );
        $parser = new EntryParser($cfp, $serviceContainer);

        $result = $parser->parse('https://sessionize.com/kanddinsky-2018/');

        sef::assertEquals('', $parser);
    }
}

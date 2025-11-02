<?php

declare(strict_types=1);
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Cli\Subcommands\Sessionize\Parser;

use Callingallpapers\Subcommands\Sessionize\Parser\Location;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Location::class)]
class LocationTest extends TestCase
{
    /** @dataProvider uriIsReturnedCorrectlyProvider */
    #[DataProvider('uriIsReturnedCorrectlyProvider')]
    public function testThatLocationIsReturnedCorrectly($file, $result)
    {
        $parser = new Location();

        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->strictErrorChecking = false;

        $dom->load($file, LIBXML_NOBLANKS ^ LIBXML_NOERROR ^ LIBXML_NOENT);

        $this->assertEquals($result, $parser->parse($dom, new DOMXPath($dom)));
    }

    public static function uriIsReturnedCorrectlyProvider() : array
    {
        return [[
            __DIR__ . '/../__assets/Azure Day Rome 2019: Call for Speakers @ Sessionize.com.html',
            'Via Avignone, 10, 00144 Roma RM',
        ], [
            __DIR__ . '/../__assets/React Week Medellín 2019: Call for Speakers @ Sessionize.com.html',
            'Medellín, Colombia',
        ],];
    }
}

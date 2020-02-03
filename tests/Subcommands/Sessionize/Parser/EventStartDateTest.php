<?php

declare(strict_types=1);
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Subcommands\Sessionize\Parser;

use Callingallpapers\Subcommands\Sessionize\Parser\EventStartDate;
use Callingallpapers\Subcommands\Sessionize\Parser\OpeningDate;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

class EventStartDateTest extends TestCase
{
    /** @dataProvider uriIsReturnedCorrectlyProvider */
    public function testThatUriIsReturnedCorrectly($file, $result)
    {
        $timezone = new DateTimeZone('Europe/Amsterdam');
        $parser = new EventStartDate($timezone);

        $dom = new DOMDocument();
        $dom->recover = true;
        $dom->strictErrorChecking = false;

        $dom->load($file, LIBXML_NOBLANKS ^ LIBXML_NOERROR ^ LIBXML_NOENT);

        $this->assertEquals($result, $parser->parse($dom, new DOMXPath($dom)));
    }

    public function uriIsReturnedCorrectlyProvider() : array
    {
        return [[
            __DIR__ . '/../__assets/Azure Day Rome 2019: Call for Speakers @ Sessionize.com.html',
            new DateTimeImmutable('2019-05-24T00:00:00.000000', new DateTimeZone('Europe/Amsterdam')),
        ], [
            __DIR__ . '/../__assets/React Week Medellín 2019: Call for Speakers @ Sessionize.com.html',
            new DateTimeImmutable('2019-03-05T00:00:00.000000', new DateTimeZone('Europe/Amsterdam')),
        ],];
    }
}

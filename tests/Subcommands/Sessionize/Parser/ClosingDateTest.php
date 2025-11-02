<?php

declare(strict_types=1);
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Cli\Subcommands\Sessionize\Parser;

use Callingallpapers\Subcommands\Sessionize\Parser\ClosingDate;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClosingDate::class)]
class ClosingDateTest extends TestCase
{
    #[DataProvider('uriIsReturnedCorrectlyProvider')]
    public function testThatUriIsReturnedCorrectly($file, $result)
    {
        $timezone = new DateTimeZone('Europe/Amsterdam');
        $parser = new ClosingDate($timezone);

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
            new DateTimeImmutable('2019-03-31T23:59:00.000000', new DateTimeZone('Europe/Amsterdam')),
        ], [
            __DIR__ . '/../__assets/React Week Medellín 2019: Call for Speakers @ Sessionize.com.html',
            new DateTimeImmutable('2019-01-29T23:59:00.000000', new DateTimeZone('Europe/Amsterdam')),
        ],];
    }
}

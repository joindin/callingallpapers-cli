<?php

declare(strict_types=1);
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Subcommands\Sessionize\Parser;

use Callingallpapers\Subcommands\Sessionize\Parser\EventUri;
use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

class EventUriTest extends TestCase
{
    /** @dataProvider uriIsReturnedCorrectlyProvider */
    public function testThatLocationIsReturnedCorrectly($file, $result)
    {
        $parser = new EventUri();

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
            'http://www.azureday.it/'
        ], [
            __DIR__ . '/../__assets/React Week Medellín 2019: Call for Speakers @ Sessionize.com.html',
            'https://reactweek.com/',
        ],];
    }
}

<?php

declare(strict_types=1);
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Subcommands\Sessionize\Parser;

use Callingallpapers\Subcommands\Sessionize\Parser\EventName;
use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

class EventNameTest extends TestCase
{
    /** @dataProvider uriIsReturnedCorrectlyProvider */
    public function testThatLocationIsReturnedCorrectly($file, $result)
    {
        $parser = new EventName();

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
            'Azure Day Rome 2019'
        ], [
            __DIR__ . '/../__assets/React Week Medellín 2019: Call for Speakers @ Sessionize.com.html',
            'React Week Medellín 2019',
        ],];
    }
}

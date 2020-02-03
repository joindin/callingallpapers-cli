<?php

declare(strict_types=1);
/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CallingallpapersTest\Subcommands\Sessionize\Parser;

use Callingallpapers\Subcommands\Sessionize\Parser\IconUri;
use DOMDocument;
use DOMXPath;
use function file_get_contents;
use PHPUnit\Framework\TestCase;

class IconUriTest extends TestCase
{
    /** @dataProvider uriIsReturnedCorrectlyProvider */
    public function testThatUriIsReturnedCorrectly($file, $result)
    {
        $parser = new IconUri('foobar');

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
            'Azure%20Day%20Rome%202019:%20Call%20for%20Speakers%20@%20Sessionize.com-Dateien/image.png'
        ], [
            __DIR__ . '/../__assets/React Week Medellín 2019: Call for Speakers @ Sessionize.com.html',
            'React%20Week%20Medell%C3%ADn%202019:%20Call%20for%20Speakers%20@%20Sessionize.com-Dateien/image.png'
        ],];
    }
}

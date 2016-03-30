<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CallingallpapersTest\Cli\Parser\Lanyrd;

use Callingallpapers\Parser\Lanyrd\EventName;
use Callingallpapers\Parser\Lanyrd\EventUri;

class EventUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider parsingEventUriProvider
     */
    public function testParsingEventUri($file, $expectedUri)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTMLFile($file);
        $dom->preserveWhiteSpace = false;

        $xpath = new \DOMXPath($dom);

        $parser = new EventUri();
        $this->assertEquals($expectedUri, $parser->parse($dom, $xpath));
    }

    public function parsingEventUriProvider()
    {
        return [
            // ['parsedFile', 'expectedEventUri'],
            [__DIR__ . '/_assets/LanyrdCfp1.html', 'http://www.lincoln.ac.uk/home/campuslife/whatson/eventsconferences/health-in-justice-conference-2016.html'],
            [__DIR__ . '/_assets/LanyrdCfp2.html', 'http://womenintechsummit.net/washington/'],
            [__DIR__ . '/_assets/LanyrdCfp3.html', 'http://www.meetup.com/Ecommerce-that-Works-Dublin/events/225344017/'],
            [__DIR__ . '/_assets/LanyrdCfp4.html', 'http://oreil.ly/1S54uk9'],
        ];
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CallingallpapersTest\Cli\Parser\Lanyrd;

use Callingallpapers\Parser\Lanyrd\ClosingDate;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ClosingDateTest extends TestCase
{
    /**
     * @dataProvider parsingClosingDateProvider
     */
    public function testParsingClosingDate($file, $expectedClosingDate)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTMLFile($file);
        $dom->preserveWhiteSpace = false;

        $xpath = new \DOMXPath($dom);

        $parser = new ClosingDate();
        $this->assertEquals($expectedClosingDate, $parser->parse($dom, $xpath));
    }

    public function parsingClosingDateProvider()
    {
        return [
            // ['parsedFile', 'expectedEventUri'],
            [__DIR__ . '/_assets/qdzmb.html', new \DateTime('2016-05-29T00:00:00+0000')],
            [__DIR__ . '/_assets/LanyrdCfp3.html', new \DateTime('2015-11-18T00:00:00+00:00')],
            [__DIR__ . '/_assets/LanyrdCfp4.html', new \DateTime('2015-11-24T00:00:00+00:00')],
            [__DIR__ . '/_assets/LanyrdCfp5.html', new \DateTime('2015-12-06T00:00:00+00:00')],
            [__DIR__ . '/_assets/LanyrdCfp6.html', new \DateTime('2015-12-09T00:00:00+00:00')],
            [__DIR__ . '/_assets/LanyrdCfp7.html', new \DateTime('2015-12-15T00:00:00+00:00')],
        ];
    }

    /**
     * @dataProvider parsingInvalidClosingDateProvider
     */
    public function testParsingInvalidClosingDate($uri)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTMLFile($uri);
        $dom->preserveWhiteSpace = false;

        $xpath = new \DOMXPath($dom);

        $parser = new ClosingDate();

        $this->expectException(InvalidArgumentException::class);
        $parser->parse($dom, $xpath);
    }

    public function parsingInvalidClosingDateProvider()
    {
        return [
            [__DIR__ . '/_assets/LanyrdCfp1.html'],
            [__DIR__ . '/_assets/LanyrdCfp2.html'],
        ];
    }
}

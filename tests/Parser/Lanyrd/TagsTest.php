<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CallingallpapersTest\Cli\Parser\Lanyrd;

use Callingallpapers\Parser\Lanyrd\Tags;

class TagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider parsingTagsProvider
     */
    public function testParsingTags($file, $expectedTags)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTMLFile($file);
        $dom->preserveWhiteSpace = false;

        $xpath = new \DOMXPath($dom);

        $parser = new Tags();
        $this->assertEquals($expectedTags, $parser->parse($dom, $xpath));
    }

    public function parsingTagsProvider()
    {
        return [
            // ['parsedFile', 'expectedEventUri'],
            [__DIR__ . '/_assets/devopsdayoslo.html', [
                'Agile',
                'Continuous Delivery',
                'DevOps',
                'Lean IT',
            ]],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParsingMissingTags()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTMLFile(__DIR__ . '/_assets/LanyrdCfp4.html');
        $dom->preserveWhiteSpace = false;

        $xpath = new \DOMXPath($dom);

        $parser = new Tags();
        $parser->parse($dom, $xpath);
    }
}

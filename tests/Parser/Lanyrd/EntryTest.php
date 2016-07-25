<?php
/**
 * Copyright (c) 2016-2016} Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2016-2016 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     30.03.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace CallingallpapersTest\Cli\Parser\Lanyrd;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Parser\Lanyrd\Entry;
use Callingallpapers\Service\TimezoneService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class EntryTest extends \PHPUnit_Framework_TestCase
{
    public function testThatLocationCanBeRetrieved()
    {
        $lat = 0;
        $lon = 0;

        $mock = new MockHandler([
            new Response(200, [], json_encode([['lat' => 0, 'lon' => 0]])),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $tzService = $this->getMockWithoutInvokingTheOriginalConstructor(TimezoneService::class);

        $entry = new Entry(new Cfp(), $client, $tzService);

        $class  = new \ReflectionClass($entry);
        $method = $class->getMethod('getLatLonForLocation');
        $method->setAccessible(true);
        $this->assertEquals([0, 0], $method->invokeArgs($entry, ['foo']));
    }
}

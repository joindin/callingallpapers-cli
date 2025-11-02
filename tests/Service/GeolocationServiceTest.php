<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
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
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     25.07.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace CallingallpapersTest\Cli\Service;

use Callingallpapers\Service\GeolocationService;
use Callingallpapers\Entity\Geolocation;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GeolocationService::class)]
#[UsesClass(Geolocation::class)]
class GeolocationServiceTest extends TestCase
{
    public function testRetrievalOfGeolocationWorks()
    {
        $client = new Client([
            'headers' => [
                'User-Agent' => 'Callingallpapers.com Location fetcher v1',
            ]
        ]);

        $tzs = new GeolocationService($client);
        $result = $tzs->getLocationForAddress('London - UK');
        try {
            $this->assertEquals(new Geolocation(51.5074456, -0.1277653), $result);
        } catch (\Exception $e) {
            $this->assertEquals(new Geolocation(51.4893335, -0.1440551), $result);
        }
    }

    #[DataProvider('fetchingLocationWithFailingHttpProvider')]
    public function testFetchingLocationWithFailingHttp($return)
    {
        $mock = new MockHandler([
            new Response($return, []),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $tzs = new GeolocationService($client);

        $this->assertEquals(new Geolocation(0, 0), $tzs->getLocationForAddress('London - UK'));
    }

    public static function fetchingLocationWithFailingHttpProvider()
    {
        return [
            '400' => [400],
            '404' => [404],
            '410' => [410],
            '418' => [418],
            '500' => [500],
        ];
    }

    #[DataProvider('fetchingLocationWithValidHttpButFailingStatusProvider')]
    public function testFetchingLocationWithValidHttpButFailingStatus($body)
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($body)),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $tzs = new GeolocationService($client);

        $this->assertEquals(new Geolocation(0, 0), $tzs->getLocationForAddress('London - UK'));
    }

    public static function fetchingLocationWithValidHttpButFailingStatusProvider()
    {
        return [
            'missing content' => [[]],
            'missing array key' => [['status' => 'bar']],
        ];
    }

    #[DataProvider('fetchingLocationWithValidHttpProvider')]
    public function testFetchingLocationWithValidHttp($body)
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($body)),
        ]);

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create($mock);
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $tzs = new GeolocationService($client);

        $this->assertEquals(new Geolocation(20, 30), $tzs->getLocationForAddress('London - UK'));
    }

    public static function fetchingLocationWithValidHttpProvider()
    {
        return [
            'Body contains timezoneName' => [[['lat' => '20', 'lon' => '30']]],
        ];
    }
}

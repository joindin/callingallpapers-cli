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

use Callingallpapers\Service\TimezoneService;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(TimezoneService::class)]
class TimezoneServiceTest extends TestCase
{
    public function testRetrievalOfTimezoneWorks()
    {
        $client = new Client();
        $key = getenv('CALLINGALLPAPERS_TIMEZONE_API_KEY');
        if (! $key) {
            $this->markTestSkipped('Skipped due to missing API-key');
        }

        if (! $key) {
            $this->markTestSkipped('No TIMEZONE-API-Key available');
        }
        $tzs = new TimezoneService($client, $key);

        $this->assertEquals('Europe/Berlin', $tzs->getTimezoneForLocation(50, 8));
    }

    #[DataProvider('fetchingTimezoneWithFailingHttpProvider')]
    public function testFetchingTimezoneWithFailingHttp($return)
    {
        $mock = new MockHandler([
            new Response($return, []),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $tzs = new TimezoneService($client, '');

        $this->assertEquals('UTC', $tzs->getTimezoneForLocation(50, 8));
    }

    public static function fetchingTimezoneWithFailingHttpProvider()
    {
        return [
            '400' => [400],
            '404' => [404],
            '410' => [410],
            '418' => [418],
            '500' => [500],
        ];
    }

    #[DataProvider('FetchingTimezoneWithValidHttpButFailingStatusProvider')]
    public function testFetchingTimezoneWithValidHttpButFailingStatus($body)
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($body)),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $tzs = new TimezoneService($client, '');

        $this->assertEquals('UTC', $tzs->getTimezoneForLocation(50, 8));
    }

    public static function fetchingTimezoneWithValidHttpButFailingStatusProvider()
    {
        return [
            'missing array key' => [['foo' => 'bar']],
            'failure array key' => [['status' => 'bar']],
        ];
    }

    #[DataProvider('FetchingTimezoneWithValidHttpProvider')]
    public function testFetchingTimezoneWithValidHttp($body)
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($body)),
        ]);

        $container = [];
        $history = Middleware::history($container);

        $handler = HandlerStack::create($mock);
        $handler->push($history);
        $client = new Client(['handler' => $handler]);

        $timezoneApiKey = getenv('CALLINGALLPAPERS_TIMEZONE_API_KEY');
        if (! $timezoneApiKey) {
            $this->markTestSkipped('Skipped due to missing API-Key');
        }
        $tzs = new TimezoneService($client, $timezoneApiKey);

        $this->assertEquals('Europe/Berlin', $tzs->getTimezoneForLocation(50, 8));

        /** @var RequestInterface $request */
        $request = $container[0]['request'];
        $this->assertEquals(
            sprintf(
                'http://api.timezonedb.com/v2/get-time-zone?key=%1$s&format=json&by=position&lat=50&lng=8',
                getenv('CALLINGALLPAPERS_TIMEZONE_API_KEY')
            ),
            (string) $request->getUri()
        );
    }

    public static function fetchingTimezoneWithValidHttpProvider()
    {
        return [
            'Body contains timezoneName' => [['status' => 'OK', 'zoneName' => 'Europe/Berlin']],
        ];
    }
}

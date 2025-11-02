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

namespace Callingallpapers\Service;

use Callingallpapers\Entity\Geolocation;
use GuzzleHttp\ClientInterface;

class GeolocationService
{
    public static $lastAccess;

    protected $uri = 'https://nominatim.openstreetmap.org/search?q=%1$s&format=jsonv2&addressdetails=1&limit=1';

    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getLocationForAddress(string $address) : Geolocation
    {
        if (self::$lastAccess === time()) {
            sleep(1);
        }
        self::$lastAccess = time();
        try {
            $result = $this->client->request('GET', sprintf(
                $this->uri,
                urlencode($address)
            ));
        } catch (\Exception $e) {
            return new Geolocation(0, 0);
        }

        $values = json_decode($result->getBody()->getContents(), true);

        if (! isset($values[0])) {
            return new Geolocation(0, 0);
        }

        if (! isset($values[0]['lat']) || ! isset($values[0]['lon'])) {
            return new Geolocation(0, 0);
        }

        return new Geolocation($values[0]['lat'], $values[0]['lon']);
    }
}

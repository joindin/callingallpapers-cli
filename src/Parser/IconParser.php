<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
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
 * @since     31.01.2017
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace Callingallpapers\Parser;

use GuzzleHttp\Client;

class IconParser
{
    private $client;

    private $dom;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
    }

    /**
     * This parser tries to identify the URI for an icon for the given Event
     *
     * For that we parse the websites sourcecode and try to find an icon.
     *
     * The URI to that Icon is then returned.
     *
     * @param string $url The URI of the event-page
     *
     * @throws \Exception should no Icon be retrievable
     * @return string
     */
    public function parse($eventPageUri)
    {
        $result = $this->client->get($eventPageUri);

        $this->dom->loadHTML($result->getBody()->getContents());

        $xpath = new \DOMXPath($this->dom);

        if ($icon = $this->getIcon($xpath)) {
            return $icon;
        }

        return $icon;
    }

    public function getIcon($xpath)
    {
        $icons = $xpath->query('icon');
    }
}

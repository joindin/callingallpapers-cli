<?php
/**
 * Copyright (c) 2015-2015 Andreas Heigl<andreas@heigl.org>
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
 * @copyright 2015-2015 Andreas Heigl/callingallpapers.com
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     06.03.2012
 * @link      http://github.com/joindin/callingallpapers
 */
namespace Callingallpapers\Parser\Lanyrd;

class EventUri
{

    public function parse($dom, $xpath)
    {
        $confPath = $xpath->query("//h3/a[contains(@class, 'summary')]");

        if (! $confPath || $confPath->length == 0) {
            throw new \InvalidArgumentException('The CfP does not seem to have an EventUri');
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');

        $content = file_Get_contents('https://lanyrd.com' . $confPath->item(0)->attributes->getNamedItem('href')->textContent);
        $content = mb_convert_encoding($content, 'UTF-8');
        $dom->loadHTML('<?xml version="1.0" charset="UTF-8" ?>' . $content);
        $dom->preserveWhiteSpace = false;

        $xpath = new \DOMXPath($dom);

        $uriPath = $xpath->query("//a[contains(@title, 'visit their website')]");

        if (! $uriPath || $uriPath->length == 0) {
            throw new \InvalidArgumentException('The CfP does not seem to have an EventUri');
        }

        return $uriPath->item(0)->attributes->getNamedItem('href')->textContent;
    }
}

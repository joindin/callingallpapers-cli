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
 * @since     23.02.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace CallingallpapersTest\Writer;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Entity\CfpList;
use Callingallpapers\Writer\ApiCfpWriter;
use GuzzleHttp\Client;

class ApiCfpWriterTest extends \PHPUnit_Framework_TestCase
{

    public function testWritingToApi()
    {
        $response = $this->getMockBuilder('\Psr\Http\Message\ResponseInterface')->getMock();
        $response->method('getStatusCode')->willReturn(200);

        $builder = $this->getMockBuilder('GuzzleHttp\Client');
        $client = $builder->getMock();
        $client->method('request')->willReturn($response);
        
        $writer = new ApiCfpWriter('http://localhost:8000/v1/cfp', '49CF885D-E0D6-4E7A-9013-C9B431B6612C', $client);

        $cfp = new Cfp();
        $cfp->description = 'description';
        $cfp->conferenceName = 'conferenceName';
        $cfp->conferenceUri = 'http://example.com/conferenceUri';
        $cfp->uri = 'http://example.com/uri';
        $cfp->dateStart = new \DateTimeImmutable('2016-01-01 12:00:00+01:00');
        $cfp->dateEnd = new \DateTimeImmutable('2016-02-01 12:00:00+01:00');
        $cfp->eventStartDate = new \DateTimeImmutable('2016-03-01 12:00:00+01:00');
        $cfp->eventEndDate = new \DateTimeImmutable('2016-04-01 12:00:00+01:00');
        $cfp->timezone = 'Europe/Berlin';
        $cfp->location = 'location';
        $cfp->latitude = 50.0;
        $cfp->longitude = 8.0;
        $cfp->tags = ['tag', 'a', 'b'];


        $this->assertTrue($writer->write($cfp));
    }
}

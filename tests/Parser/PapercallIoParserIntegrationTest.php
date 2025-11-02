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
 * @since     05.07.2017
 * @link      http://github.com/heiglandreas/callingallpapers_cli
 */

namespace CallingallpapersTest\Cli\Parser;

use Callingallpapers\Entity\Geolocation;
use Callingallpapers\Parser\PapercallIo\EventParser;
use Callingallpapers\Parser\PapercallIo\PapercallIoParser;
use Callingallpapers\Parser\PapercallIo\PapercallIoParserFactory;
use Callingallpapers\Service\GeolocationService;
use Callingallpapers\Service\TimezoneService;
use Callingallpapers\Writer\TestCfpWriter;
use Callingallpapers\Writer\WriterInterface;
use Mockery as M;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\StreamOutput;

#[CoversClass(PapercallIoParser::class)]
class PapercallIoParserIntegrationTest extends TestCase
{
    public function testThatParsingFirstPageWorks()
    {
        $this->markTestSkipped('Skipped due to current refactoring at papercall.io');
        $loc = M::mock(GeolocationService::class);
        $loc->shouldReceive('getLocationForAddress')->andReturn(new Geolocation(0, 0));

        $tz = M::mock(TimezoneService::class);
        $tz->shouldReceive('getTimezoneForLocation')->andReturn('UTC');

        $parser = (new PapercallIoParserFactory($tz, $loc))();

        $writer = new TestCfpWriter();
        $parser->parse($writer);

        $this->assertGreaterThan(0, $writer->count());
        $this->assertTrue($writer->valid());
    }
}

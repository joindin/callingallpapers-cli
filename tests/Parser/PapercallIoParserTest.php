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

use Callingallpapers\Parser\PapercallIo\EventParser;
use Callingallpapers\Parser\PapercallIo\PapercallIoParser;
use Callingallpapers\Service\TimezoneService;
use Callingallpapers\Writer\WriterInterface;
use Mockery as M;
use PHPUnit\Framework\TestCase;

class PapercallIoParserTest extends TestCase
{
    public function testThatParsingFirstPageWorks()
    {
        $eventParser = M::mock(EventParser::class);
        $eventParser->shouldReceive('parseEvent')->times(100);

        $writer = M::mock(WriterInterface::class);
        $writer->shouldReceive('write')->times(100);

        $parser = new PapercallIoParser($eventParser);
        $parser->setStartUrl(__DIR__ . '/PapercallIo/_assets/index2.html');

        self::assertTrue($parser->parse($writer));
    }
}

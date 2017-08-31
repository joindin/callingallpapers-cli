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
 * @since     07.07.2017
 * @link      http://github.com/heiglandreas/callingallpapers_cli
 */

namespace Callingallpapers\Parser\PapercallIo;

use Callingallpapers\Parser\EventDetailParserList;
use Callingallpapers\Parser\TimezoneFromLocationFetcher;
use Callingallpapers\Service\GeolocationService;
use Callingallpapers\Service\TimezoneService;

class PapercallIoParserFactory
{
    private $timezoneService;

    private $geolocationService;

    private $timezoneFromlocationFetcher;

    public function __construct(TimezoneService $tzservice, GeolocationService $geolocationService)
    {
        $this->timezoneService = $tzservice;
        $this->geolocationService = $geolocationService;

        $this->timezoneFromlocationFetcher = new TimezoneFromLocationFetcher(
            $this->timezoneService,
            $this->geolocationService
        );
    }

    public function __invoke()
    {
        $detailsParserList = new EventDetailParserList();

        $detailsParserList
            ->addEventDetailParser(new Location())
            ->addEventDetailParser($this->timezoneFromlocationFetcher)
            ->addEventDetailParser(new ClosingDate())
            ->addEventDetailParser(new Description())
            ->addEventDetailParser(new EventName())
            ->addEventDetailParser(new Tags())
            ->addEventDetailParser(new EventStartDate())
            ->addEventDetailParser(new EventEndDate())
            ->addEventDetailParser(new Uri())
            ->addEventDetailParser(new EventUri())
            ->addEventDetailParser(new Icon())
        ;

        $eventParser = new EventParser($detailsParserList);

        return new PapercallIoParser($eventParser);
    }
}

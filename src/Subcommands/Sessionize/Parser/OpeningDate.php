<?php

declare(strict_types=1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */
namespace Callingallpapers\Subcommands\Sessionize\Parser;

use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use function str_replace;
use function trim;

class OpeningDate
{
    const OPENING_STRING = 'CfS opens at';

    protected $timezone;

    public function __construct(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    public function parse(DOMDocument $dom, DOMXPath $xpath) : DateTimeImmutable
    {
        $openingDateHolder = $xpath->query('//div[./div/span[contains(text(), "' . self::OPENING_STRING . '")]]');

        if (! $openingDateHolder || $openingDateHolder->length == 0) {
            throw new InvalidArgumentException('No CfP-Open Date found');
        }

        $openingDay  = $openingDateHolder->item(0)->getElementsByTagName('h2')->item(0)->textContent;
        $openingHour = $openingDateHolder->item(0)->getElementsByTagName('span')->item(0)->textContent;

        $openingHour = $this->clearOpeningHour($openingHour);

        return new DateTimeImmutable($openingDay. ' ' . $openingHour, $this->timezone);
    }

    private function clearOpeningHour(string $openingHour) : string
    {
        return trim(str_replace(self::OPENING_STRING, '', $openingHour));
    }
}

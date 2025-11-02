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

class ClosingDate
{
    protected $timezone;

    public function __construct(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    public function parse(DOMDocument $dom, DOMXPath $xpath) : DateTimeImmutable
    {
        $closingDateHolder = $xpath->query('//div[./div/span[contains(text(), "Call closes at")]] | //div[./div/span[contains(text(), "CfS closes at")]]');

        if (! $closingDateHolder || $closingDateHolder->length == 0) {
            throw new InvalidArgumentException('The CfP does not seem to have a closing date');
        }

        $closingDay = $closingDateHolder->item(0)->getElementsByTagName('h2')->item(0)->textContent;
        $closingHour = str_replace('Call closes at ', '', $closingDateHolder->item(0)->getElementsByTagName('span')->item(0)->textContent);

        $closingHour = $this->clearClosingHour($closingHour);

        return new DateTimeImmutable($closingDay . ' ' . $closingHour, $this->timezone);
    }

    private function clearClosingHour(string $closingHour) : string
    {
        return trim(str_replace('CfS closes at', '', $closingHour));
    }
}

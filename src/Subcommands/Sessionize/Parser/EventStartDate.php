<?php

declare(strict_types = 1);

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

class EventStartDate
{
    protected $timezone;

    public function __construct(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    public function parse(DOMDocument $dom, DOMXPath $xpath) : DateTimeImmutable
    {
        // This expression does not work. It looks like the reason is the array-notation...
        //$startDate = $xpath->query('//div[contains(text()[2], "event starts")]/following-sibling::h2');
        $startDate = $xpath->query('//div[contains(., "event starts")]');

        if (! $startDate || $startDate->length == 0) {
            // This expression does not work. It looks like the reason is the array-notation...
            //$startDate = $xpath->query('//div[contains(text()[2], "event date")]/following-sibling::h2');
            $startDate = $xpath->query('//div[contains(., "event date")]');
        }
        if (! $startDate || $startDate->length == 0) {
            // This expression does not work. It looks like the reason is the array-notation...
            //$startDate = $xpath->query('//div[contains(text()[2], "event date")]/following-sibling::h2');
            $startDate = $xpath->query('//div[contains(., "planned future dates")]');
        }
        if (! $startDate || $startDate->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have a start date-identifier');
        }

        $startDate = $xpath->query('h2', $startDate->item($startDate->length-1)->parentNode);

        if (! $startDate || $startDate->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have a start date');
        }

        $startDate = $startDate->item(0)->textContent;

        // Make sure that with multiple start-dates separated by a comma
        // only the first one is used
        $startDate = explode(', ', $startDate);

        return new DateTimeImmutable($startDate[0], $this->timezone);
    }
}

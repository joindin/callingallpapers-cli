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
        $startDate = $xpath->query('//div[contains(text(), "event starts")]');

        if (! $startDate || $startDate->length == 0) {
            // This expression does not work. It looks like the reason is the array-notation...
            //$startDate = $xpath->query('//div[contains(text()[2], "event date")]/following-sibling::h2');
            $startDate = $xpath->query('//div[contains(text(), "event date")]');
        }
        if (! $startDate || $startDate->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have a start date');
        }

        $startDate = $xpath->query('h2', $startDate->item(0)->parentNode);

        if (! $startDate || $startDate->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have a start date');
        }

        $startDate = $startDate->item(0)->textContent;

        return new DateTimeImmutable($startDate, $this->timezone);
    }
}

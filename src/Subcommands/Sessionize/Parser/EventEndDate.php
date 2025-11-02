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

class EventEndDate
{
    protected $timezone;

    public function __construct(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    public function parse(DOMDocument $dom, DOMXPath $xpath) : DateTimeImmutable
    {
        // This expression does not work. It looks like the reason is the array-notation...
        //$endDate = $xpath->query('//div[contains(text()[2], "event ends")]/following-sibling::h2');
        $endDate = $xpath->query('//div[contains(text(), "event ends")]');

        if (! $endDate || $endDate->length == 0) {
            // This expression does not work. It looks like the reason is the array-notation...
            //$endDate = $xpath->query('//div[contains(text()[2], "event date")]/following-sibling::h2');
            $endDate = $xpath->query('//div[contains(text(), "event date")]');
        }
        if (! $endDate || $endDate->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have an end date');
        }

        $endDate = $xpath->query('h2', $endDate->item(0)->parentNode);

        if (! $endDate || $endDate->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have an end date');
        }

        $endDate = $endDate->item(0)->textContent;

        return new DateTimeImmutable($endDate, $this->timezone);
    }
}

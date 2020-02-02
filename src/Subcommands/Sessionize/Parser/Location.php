<?php

declare(strict_types=1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */
namespace Callingallpapers\Subcommands\Sessionize\Parser;

use DOMDocument;
use DOMXPath;

class Location
{
    public function parse(DOMDocument $dom, DOMXPath $xpath) : string
    {
        // This expression does not work. It looks like the reason is the array-notation...
        //$locations = $xpath->query('//div[contains(text()[2], "location")]/following-sibling::h2/span');
        $locationMarker = $xpath->query("//div[contains(., 'location')]");
        if (! $locationMarker || $locationMarker->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have a locationMarker');
        }

        $locations = $xpath->query('//h2/span', $locationMarker->item(0)->parentNode);

        if (! $locations || $locations->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have a location');
        }
        $location = [];
        foreach ($locations as $item) {
            $location = trim($item->textContent);
            if ($location === '') {
                continue;
            }

            return $location;

            $location[] = $location;
        }

        return implode(', ', array_unique($location));
    }
}

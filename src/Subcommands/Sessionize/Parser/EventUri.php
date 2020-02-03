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

class EventUri
{

    public function parse(DOMDocument $dom, DOMXPath $xpath)
    {
        // This expression does not work. It looks like the reason is the array-notation...
        //$uriPath = $xpath->query('//div[contains(text()[2], "website")]/following-sibling::h2/a');
        $uriPath = $xpath->query('//div[contains(., "website")]');

        if (! $uriPath || $uriPath->length == 0) {
            throw new \InvalidArgumentException('The CfP does not seem to have an EventUri');
        }

        $uriPath = $xpath->query('.//h2/a', $uriPath->item(0)->parentNode);

        if (! $uriPath || $uriPath->length == 0) {
            throw new \InvalidArgumentException('The Event does not seem to have a location');
        }

        return $uriPath->item(0)->attributes->getNamedItem('href')->textContent;
    }
}

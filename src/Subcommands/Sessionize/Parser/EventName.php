<?php

declare(strict_types = 1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Callingallpapers\Subcommands\Sessionize\Parser;

use DOMDocument;
use DOMXPath;

class EventName
{
    public function parse(DOMDocument $dom, DOMXPath $xpath)
    {
        $confPath = $xpath->query('//h4');

        if (! $confPath || $confPath->length == 0) {
            throw new \InvalidArgumentException('The CfP does not seem to have an eventname');
        }

        return trim($confPath->item(0)->textContent);
    }
}

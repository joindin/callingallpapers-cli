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

class IconUri
{
    private $baseUri;

    public function __construct(string $baseUri)
    {
        $this->baseUri  = $baseUri;
    }

    public function parse(DOMDocument $dom, DOMXPath $xpath) : string
    {
        $uriPath = $xpath->query('//div[contains(@class, "ibox-content")]/img');

        if (! $uriPath || $uriPath->length == 0) {
            throw new \InvalidArgumentException('The CfP does not seem to have an Icon');
        }

        return $uriPath->item(0)->attributes->getNamedItem('src')->textContent;
    }
}

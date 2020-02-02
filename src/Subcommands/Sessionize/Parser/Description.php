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
use function preg_replace;
use function strip_tags;

class Description
{
    public function parse(DOMDocument $dom, DOMXPath $xpath)
    {
        $result = $xpath->query('//hr[contains(@class, "m-t-none")]');
        if ($result->length < 1) {
            return '';
        }

        $text = [];
        for ($i = 0; $i < $result->length; $i++) {
            $resultI = $result->item($i)->parentNode->childNodes;

            if ($resultI->length <= 0) {
                return '';
            }

            foreach ($resultI as $node) {
                $text[] = $dom->saveXML($node);
            }
        }
        $description = trim(implode('', $text));
        $description = preg_replace(['/\<\!\-\-.*?\-\-\>/si', '/\<script.*?\<\/script\>/si'],'', $description);

        return $description;
    }
}

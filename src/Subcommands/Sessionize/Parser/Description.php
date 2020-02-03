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
use function str_replace;
use function strip_tags;
use function strpos;
use function var_dump;

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
            $currentItem = $result->item($i);
            $resultI = $currentItem->parentNode->childNodes;
            if ($currentItem->childNodes->length > 0) {
                $resultI = $currentItem->childNodes;
            }

            if ($resultI->length <= 0) {
                continue;
            }
            foreach ($resultI as $key => $node) {
                $nodeText = trim($dom->saveXML($node));

                if (false !== strpos($nodeText, 'submit-your-session-button')) {
                    continue;
                }

                $nodeText = str_replace('</hr>', '', $nodeText);
                $nodeText = str_replace('<hr class="m-t-none">', '', $nodeText);
                $text[] = $nodeText;
            }
        }
        $description = trim(implode('', $text));
        $description = preg_replace(['/\<\!\-\-.*?\-\-\>/si', '/\<script.*?\<\/script\>/si', '/\<script.*?src=\".*?\/\>/'], '', $description);

        return $description;
    }
}

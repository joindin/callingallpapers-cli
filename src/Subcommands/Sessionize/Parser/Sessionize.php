<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\Subcommands\Sessionize\Parser;

use Callingallpapers\Entity\CfpList;
use Callingallpapers\Parser\ParserInterface;
use Callingallpapers\Writer\WriterInterface;
use DOMDocument;
use DOMElement;
use DOMXPath;

class Sessionize implements ParserInterface
{
    private $parser;

    public function __construct(EntryParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param WriterInterface $output
     *
     * @return CfpList
     */
    public function parse(WriterInterface $writer)
    {
        $uri = 'https://sessionize.com/sitemap/events.xml';

        $cfpList = new CfpList();

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->load($uri);
        $dom->preserveWhiteSpace = false;
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('x', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $nodes = $xpath->query("//x:url");
        if ($nodes->length < 1) {
            return $cfpList;
        }

        /** @var DOMElement $node */
        foreach ($nodes as $node) {
            $priority = $node->getElementsByTagName('priority');
            $priority = $priority->item(0);
            if ((float)$priority->textContent <= 0.6) {
                continue;
            }

            $loc = $node->getElementsByTagName('loc');
            $loc = $loc->item(0);

            if (! $loc->textContent) {
                continue;
            }

            try {
                $cfp = $this->parser->parse($loc->textContent);
                $writer->write($cfp, 'Sessionize');
                $cfpList->append($cfp);
            } catch (\Exception $e) {
                error_log($e->getMEssage() . ' ' . $loc->textContent);
            }
        }

        return $cfpList;
    }
}

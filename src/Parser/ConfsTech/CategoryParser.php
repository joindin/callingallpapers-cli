<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\Parser\ConfsTech;

use Callingallpapers\ResultKeeper\ResultKeeper;
use Callingallpapers\Writer\WriterInterface;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CategoryParser
{
    private $conferenceParser;

    private $writer;

    private $client;

    public function __construct(
        ConferenceParser $conferenceParser,
        Client $client,
        WriterInterface $writer
    ) {
        $this->conferenceParser = $conferenceParser;
        $this->writer           = $writer;
        $this->client           = $client;
    }

    public function __invoke(string $category, string $url)
    {
        $cat = $this->client->request('GET', $url);

        $cat = json_decode($cat->getBody()->getContents(), true);
        if (isset($cat['encoding']) && $cat['encoding'] === 'base64') {
            $cat = json_decode(base64_decode($cat['content']), true);
        }
        foreach ($cat as $conference) {
            if (! isset($conference['cfpEndDate'])) {
                continue;
            }
            try {
                $cfp = ($this->conferenceParser)($conference);
                $cfp->addTag($category);
                $this->writer->write($cfp, 'confs.tech');
            } catch (Exception $e) {
                // Nothing to be done. We just skip over.
            }
        }
    }
}

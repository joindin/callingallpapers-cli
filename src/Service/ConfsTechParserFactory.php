<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\Service;

use Callingallpapers\Parser\ConfsTech\CategoryParser;
use Callingallpapers\Parser\ConfsTech\ConferenceParser;
use Callingallpapers\Parser\ConfsTech\MainParser;
use Callingallpapers\Parser\ConfsTech\YearParser;
use Callingallpapers\Writer\WriterInterface;
use DateTimeImmutable;
use GuzzleHttp\Client;
use Symfony\Component\Console\Output\OutputInterface;

class ConfsTechParserFactory
{
    private $client;

    private $conferenceParser;

    private $output;

    public function __construct(
        ConferenceParser $conferenceParser,
        Client $client,
        WriterInterface $output
    ) {
        $this->client = $client;
        $this->conferenceParser = $conferenceParser;
        $this->output = $output;
    }

    public function createParser(WriterInterface $writer) : MainParser
    {
        $catParser  = new CategoryParser($this->conferenceParser, $this->client, $this->output);
        $yearParser = new YearParser($catParser, $this->client);

        return new MainParser(
            $yearParser,
            $this->client,
            (new DateTimeImmutable())->format('Y')
        );
    }
}

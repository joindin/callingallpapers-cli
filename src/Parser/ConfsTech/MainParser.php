<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\Parser\ConfsTech;

use DateTimeImmutable;
use GuzzleHttp\Client;

class MainParser
{
    private $yearParser;

    private $client;

    private $startYear;

    public function __construct(
        YearParser $yearParser,
        Client $client,
        string $startYear
    ) {
        $this->yearParser = $yearParser;
        $this->client     = $client;
        $this->startYear  = $startYear;
    }

    public function __invoke()
    {
        $years = $this->client->request(
            'GET',
            'https://api.github.com/repos/tech-conferences/conference-data/contents/conferences'
        );

        $years = json_decode($years->getBody()->getContents(), true);
        foreach ($years as $year) {
            if ($year['name'] < $this->startYear) {
                continue;
            }
            ($this->yearParser)($year['url']);
        }
    }
}

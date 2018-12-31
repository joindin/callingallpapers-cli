<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\Parser\ConfsTech;

use GuzzleHttp\Client;

class YearParser
{
    private $client;

    private $parser;

    public function __construct(
        CategoryParser $parser,
        Client $client
    ) {
        $this->client = $client;
        $this->parser = $parser;
    }

    public function __invoke(string $url)
    {
        $categories = $this->client->request('GET', $url);

        $categories = json_decode($categories->getBody()->getContents(), true);
        foreach ($categories as $category) {
            ($this->parser)(
                str_replace('.json', '', $category['name']),
                $category['git_url']
            );
        }
    }
}

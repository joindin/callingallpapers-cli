<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace CallingallpapersTest\Cli\Parser\ConfsTech;

use Callingallpapers\Parser\ConfsTech\CategoryParser;
use Callingallpapers\Parser\ConfsTech\YearParser;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Mockery as M;

class YearParserTest extends TestCase
{
    private $categoryParser;

    private $parser;

    private $client;

    public function setup(): void
    {
        $this->categoryParser = M::mock(CategoryParser::class);
        $this->client         = M::mock(Client::class);
        $this->parser         = new YearParser($this->categoryParser, $this->client);
    }

    /**
     * @covers \Callingallpapers\Parser\ConfsTech\YearParser::__invoke
     */
    public function testInvokation()
    {
        $body = M::mock(StreamInterface::class);
        $body->shouldReceive('getContents')->andReturn('[
  {
    "type": "file",
    "size": 625,
    "name": "octokit.rb",
    "path": "lib/octokit.rb",
    "sha": "fff6fe3a23bf1c8ea0692b4a883af99bee26fd3b",
    "url": "https://api.github.com/repos/octokit/octokit.rb/contents/lib/octokit.rb",
    "git_url": "https://api.github.com/repos/octokit/octokit.rb/git/blobs/fff6fe3a23bf1c8ea0692b4a883af99bee26fd3b",
    "html_url": "https://github.com/octokit/octokit.rb/blob/master/lib/octokit.rb",
    "download_url": "https://raw.githubusercontent.com/octokit/octokit.rb/master/lib/octokit.rb",
    "_links": {
      "self": "https://api.github.com/repos/octokit/octokit.rb/contents/lib/octokit.rb",
      "git": "https://api.github.com/repos/octokit/octokit.rb/git/blobs/fff6fe3a23bf1c8ea0692b4a883af99bee26fd3b",
      "html": "https://github.com/octokit/octokit.rb/blob/master/lib/octokit.rb"
    }
  },
  {
    "type": "dir",
    "size": 0,
    "name": "octokit.json",
    "path": "lib/octokit",
    "sha": "a84d88e7554fc1fa21bcbc4efae3c782a70d2b9d",
    "url": "https://api.github.com/repos/octokit/octokit.rb/contents/lib/octokit",
    "git_url": "https://api.github.com/repos/octokit/octokit.rb/git/trees/a84d88e7554fc1fa21bcbc4efae3c782a70d2b9d",
    "html_url": "https://github.com/octokit/octokit.rb/tree/master/lib/octokit",
    "download_url": null,
    "_links": {
      "self": "https://api.github.com/repos/octokit/octokit.rb/contents/lib/octokit",
      "git": "https://api.github.com/repos/octokit/octokit.rb/git/trees/a84d88e7554fc1fa21bcbc4efae3c782a70d2b9d",
      "html": "https://github.com/octokit/octokit.rb/tree/master/lib/octokit"
    }
  }
]');

        $response = M::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')->andReturn($body);

        $this->client->shouldReceive('request')->with('GET', 'https://test.example.com')->andReturn($response);

        $this->categoryParser->shouldReceive('__invoke')->once()->with(
            'octokit.rb',
            'https://api.github.com/repos/octokit/octokit.rb/git/blobs/fff6fe3a23bf1c8ea0692b4a883af99bee26fd3b'
        );
        $this->categoryParser->shouldReceive('__invoke')->once()->with(
            'octokit',
            'https://api.github.com/repos/octokit/octokit.rb/git/trees/a84d88e7554fc1fa21bcbc4efae3c782a70d2b9d'
        );

        self::assertNull(($this->parser)('https://test.example.com'));
    }

    /**
     * @covers \Callingallpapers\Parser\ConfsTech\YearParser::__construct
     */
    public function testConstruction()
    {
        self::assertInstanceOf(YearParser::class, $this->parser);
    }
}

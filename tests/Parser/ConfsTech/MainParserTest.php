<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace CallingallpapersTest\Cli\Parser\ConfsTech;

use Callingallpapers\Parser\ConfsTech\MainParser;
use Callingallpapers\Parser\ConfsTech\YearParser;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Mockery as M;

class MainParserTest extends TestCase
{
    private $yearParser;

    private $client;

    private $parser;

    public function setup()
    {
        $this->yearParser = M::mock(YearParser::class);
        $this->client     = M::mock(Client::class);
        $this->parser     = new MainParser($this->yearParser, $this->client, '2018');
    }

    /**
     * @covers \Callingallpapers\Parser\ConfsTech\MainParser::__construct
     */
    public function testConstruction()
    {
        self::assertAttributeSame($this->yearParser, 'yearParser', $this->parser);
        self::assertAttributeSame($this->client, 'client', $this->parser);
    }

    /**
     * @covers \Callingallpapers\Parser\ConfsTech\MainParser::__invoke
     */
    public function testInvokation()
    {
        $body = M::mock(StreamInterface::class);
        $body->shouldReceive('getContents')->andReturn('[
  {
    "name": "2017",
    "path": "conferences/2017",
    "sha": "c911c06be1ee15e692ff0c3d85842536dda45875",
    "size": 0,
    "url": "https://api.github.com/repos/tech-conferences/conference-data/contents/conferences/2017?ref=master",
    "html_url": "https://github.com/tech-conferences/conference-data/tree/master/conferences/2017",
    "git_url": "https://api.github.com/repos/tech-conferences/conference-data/git/trees/c911c06be1ee15e692ff0c3d85842536dda45875",
    "download_url": null,
    "type": "dir",
    "_links": {
      "self": "https://api.github.com/repos/tech-conferences/conference-data/contents/conferences/2017?ref=master",
      "git": "https://api.github.com/repos/tech-conferences/conference-data/git/trees/c911c06be1ee15e692ff0c3d85842536dda45875",
      "html": "https://github.com/tech-conferences/conference-data/tree/master/conferences/2017"
    }
  },
  {
    "name": "2018",
    "path": "conferences/2018",
    "sha": "ec2ff7617ccd40bb299e72546025362bf8f3a2e2",
    "size": 0,
    "url": "https://api.github.com/repos/tech-conferences/conference-data/contents/conferences/2018?ref=master",
    "html_url": "https://github.com/tech-conferences/conference-data/tree/master/conferences/2018",
    "git_url": "https://api.github.com/repos/tech-conferences/conference-data/git/trees/ec2ff7617ccd40bb299e72546025362bf8f3a2e2",
    "download_url": null,
    "type": "dir",
    "_links": {
      "self": "https://api.github.com/repos/tech-conferences/conference-data/contents/conferences/2018?ref=master",
      "git": "https://api.github.com/repos/tech-conferences/conference-data/git/trees/ec2ff7617ccd40bb299e72546025362bf8f3a2e2",
      "html": "https://github.com/tech-conferences/conference-data/tree/master/conferences/2018"
    }
  },
  {
    "name": "2019",
    "path": "conferences/2019",
    "sha": "6ea152d2c98143f2db3fc284f60be42ac7de5e94",
    "size": 0,
    "url": "https://api.github.com/repos/tech-conferences/conference-data/contents/conferences/2019?ref=master",
    "html_url": "https://github.com/tech-conferences/conference-data/tree/master/conferences/2019",
    "git_url": "https://api.github.com/repos/tech-conferences/conference-data/git/trees/6ea152d2c98143f2db3fc284f60be42ac7de5e94",
    "download_url": null,
    "type": "dir",
    "_links": {
      "self": "https://api.github.com/repos/tech-conferences/conference-data/contents/conferences/2019?ref=master",
      "git": "https://api.github.com/repos/tech-conferences/conference-data/git/trees/6ea152d2c98143f2db3fc284f60be42ac7de5e94",
      "html": "https://github.com/tech-conferences/conference-data/tree/master/conferences/2019"
    }
  }
]');

        $response = M::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')->andReturn($body);

        $this->client->shouldReceive('request')->with(
            'GET',
            'https://api.github.com/repos/tech-conferences/conference-data/contents/conferences'
        )->andReturn($response);

        $this->yearParser->shouldReceive('__invoke')->once()->with(
            'https://api.github.com/repos/tech-conferences/conference-data/contents/conferences/2018?ref=master'
        );
        $this->yearParser->shouldReceive('__invoke')->once()->with(
            'https://api.github.com/repos/tech-conferences/conference-data/contents/conferences/2019?ref=master'
        );

        self::assertNull(($this->parser)());
    }
}

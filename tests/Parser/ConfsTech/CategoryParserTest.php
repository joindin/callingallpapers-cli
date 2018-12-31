<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace CallingallpapersTest\Cli\Parser\ConfsTech;

use Callingallpapers\Entity\Cfp;
use Callingallpapers\Parser\ConfsTech\CategoryParser;
use Callingallpapers\Parser\ConfsTech\ConferenceParser;
use Callingallpapers\Writer\WriterInterface;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Mockery as M;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CategoryParserTest extends TestCase
{
    private $conferenceParser;

    private $writer;

    private $client;

    private $parser;

    public function setup()
    {
        $this->conferenceParser = M::mock(ConferenceParser::class);
        $this->writer = M::mock(WriterInterface::class);
        $this->client = M::mock(Client::class);

        $this->parser = new CategoryParser($this->conferenceParser, $this->client, $this->writer);
    }
    /**
     * @covers \Callingallpapers\Parser\ConfsTech\CategoryParser::__construct
     */
    public function testConstructor()
    {
        self::assertAttributeSame($this->conferenceParser, 'conferenceParser', $this->parser);
        self::assertAttributeSame($this->writer, 'writer', $this->parser);
    }

    /**
     * @covers \Callingallpapers\Parser\ConfsTech\CategoryParser::__invoke
     */
    public function testInvokation()
    {
        $body = M::mock(StreamInterface::class);
        $body->shouldReceive('getContents')->andReturn(json_encode([[
            'cfpEndDate' => 'foo',
        ],[]]));

        $response = M::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')->andReturn($body);

        $this->client->shouldReceive('request')->with('GET', 'https://test.example.com')->andReturn($response);

        $this->conferenceParser->shouldReceive('__invoke')->once()->andReturn(new Cfp());

        $cfp = new Cfp();
        $cfp->addTag('category');
        $this->writer->shouldReceive('write')->once()->with($cfp);

        self::assertNull(($this->parser)('category', 'https://test.example.com'));
    }
}

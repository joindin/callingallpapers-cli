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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Mockery as M;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(CategoryParser::class)]
class CategoryParserTest extends TestCase
{
    /** @var ConferenceParser|M\LegacyMockInterface|M\MockInterface  */
    private $conferenceParser;

    /** @var WriterInterface|M\LegacyMockInterface|M\MockInterface  */
    private $writer;

    /** @var Client|M\LegacyMockInterface|M\MockInterface  */
    private $client;

    /** @var CategoryParser */
    private $parser;

    public function setup(): void
    {
        $this->conferenceParser = M::mock(ConferenceParser::class);
        $this->writer = M::mock(WriterInterface::class);
        $this->client = M::mock(Client::class);

        $this->parser = new CategoryParser($this->conferenceParser, $this->client, $this->writer);
    }

    public function testConstructor()
    {
        self::assertInstanceOf(CategoryParser::class, $this->parser);
    }

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

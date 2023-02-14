<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\Subcommands\SessionizeApi\Parser;

use Callingallpapers\Entity\CfpList;
use Callingallpapers\Parser\ParserInterface;
use Callingallpapers\Service\ConfigService;
use Callingallpapers\Writer\WriterInterface;
use DOMDocument;
use DOMElement;
use DOMXPath;
use GuzzleHttp\Client;
use function json_decode;

class Sessionize implements ParserInterface
{
    private $parser;

    private $client;

    private $configService;

    public function __construct(EntryParser $parser, Client $client, ConfigService $configService)
    {
        $this->parser = $parser;
        $this->client = $client;
        $this->configService = $configService;
    }

    /**
     * @param WriterInterface $output
     *
     * @return CfpList
     */
    public function parse(WriterInterface $writer)
    {
        $uri = 'https://sessionize.com/api/universal/open-cfps';

        $response = $this->client->get($uri, [
            'headers' => [
                'X-API-KEY' => $this->configService->getConfiguration()['sessionize.apiKey'],
            ],
        ]);

        $events = json_decode($response->getBody()->getContents(), true);

        $cfpList = new CfpList();

        /**
         * @var array<array{
         *      eventId: int,
         *      name: string,
         *      organizer: string,
         *      website: string,
         *      cfpLink: string,
         *      isTest: boolean,
         *      isOnline: boolean,
         *      isUserGroup: boolean,
         *      expensesCovered: array{
         *          conferenceFee: boolean,
         *          accommodation: boolean,
         *          travel: boolean
         *      },
         *      eventDates: array{
         *          start: datestring,
         *          end: datestring
         *      },
         *      cfpDates: array{
         *          startUtc: datetimestringZ,
         *          endUtc: datetimestringZ,
         *          start: datetimestring,
         *          end: datetimestring
         *      },
         *      timezone: array{
         *          iana: string,
         *          windows: string
         *      },
         *      location: array{
         *          full: string,
         *          city: string,
         *          state: string,
         *          country: string,
         *          coordinates: latlonstring
         *      },
         *      links: array{
         *          twitter: string,
         *          linkedIn: null,
         *          facebook: string,
         *          instagram: string,
         *      }
         *  }> $events
         */
        foreach ($events as $event) {
            // when the event is a usergroup we ignore it.
            if ($event['isUserGroup'] === true) {
                continue;
            }

            // When the event is a test, we remove it
            if ($event['isTest'] === true) {
                continue;
            }

            // We are not interersted in events without a CFP-Url
            if (($event['cfpLink']??'') === '') {
                continue;
            }

            // We are not interested in Events without a website
            if (($event['website']??'') === '') {
                continue;
            }

            try {
                $cfp = $this->parser->parse($event);
                $writer->write($cfp, 'Sessionize');
                $cfpList->append($cfp);
            } catch (\Exception $e) {
                error_log($e->getMessage());
            }
        }

        return $cfpList;
    }
}

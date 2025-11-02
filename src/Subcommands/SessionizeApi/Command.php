<?php

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licensed under the MIT-license. For details see the included file LICENSE.md
 */
declare(strict_types=1);

namespace Callingallpapers\Subcommands\SessionizeApi;

use Callingallpapers\Command\AbstractParseEvents;
use Callingallpapers\Entity\Cfp;
use Callingallpapers\Parser\ParserInterface;
use Callingallpapers\Service\ConfigService;
use Callingallpapers\Service\ServiceContainer;
use Callingallpapers\Subcommands\SessionizeApi\Parser\EntryParser;
use Callingallpapers\Subcommands\SessionizeApi\Parser\Sessionize;
use function strtolower;

class Command extends AbstractParseEvents
{
    const NAME = 'SessionizeAPI';

    protected function getParser(ServiceContainer $serviceContainer) : ParserInterface
    {
        $parser = new EntryParser(new Cfp(), $serviceContainer);

        return new Sessionize($parser, $serviceContainer->getClient(), new ConfigService());
    }

    protected function getParserName() : string
    {
        return self::NAME;
    }

    protected function getParserId() : string
    {
        return strtolower($this->getParserName());
    }

    protected function getServiceUrl() : string
    {
        return 'https://sessionize.com';
    }
}

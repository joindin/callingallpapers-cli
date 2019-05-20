<?php
/**
 * Copyright (c) 2015-2016 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2015-2016 Andreas Heigl/callingallpapers.com
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     01.12.2015
 * @link      http://github.com/heiglandreas/callingallpapers-cli
 */
namespace Callingallpapers\Command;

use Callingallpapers\CfpFilter\FilterList;
use Callingallpapers\CfpFilter\FollowUriRedirect;
use Callingallpapers\CfpFilter\StripParamsFromUri;
use Callingallpapers\ErrorHandling\ErrorStore;
use Callingallpapers\Exception\UnverifiedUriException;
use Callingallpapers\Parser\ConfsTech\ConferenceParser;
use Callingallpapers\Parser\ConfsTechParser;
use Callingallpapers\Parser\JoindinCfpParser;
use Callingallpapers\Parser\Lanyrd\LanyrdCfpParser;
use Callingallpapers\Parser\PapercallIo\PapercallIoParserFactory;
use Callingallpapers\Parser\ParserInterface;
use Callingallpapers\ResultKeeper\ConsoleOutputResultKeeper;
use Callingallpapers\ResultKeeper\ResultKeeper;
use Callingallpapers\Service\ConfsTechParserFactory;
use Callingallpapers\Service\GeolocationService;
use Callingallpapers\Service\ServiceContainer;
use Callingallpapers\Service\TimezoneService;
use Callingallpapers\Writer\ApiCfpWriter;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractParseEvents extends Command
{
    protected function configure()
    {
        $this->setName(sprintf("parseCfPs:%s", $this->getParserId()))
            ->setDescription(sprintf("Retrieve CfPs from %s only and parse them", $this->getParserName()))
            ->setDefinition(array(
                new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'What should be the first date to be taken into account?', ''),
            ))
            ->setHelp(sprintf(<<<EOT
Get details about CfPs from https://sesionize.com

Usage:

<info>callingallpapers parseCfPs:sessionize 2015-02-23<env></info>

If you ommit the date the current date will be used instead
<info>callingallpapers parseCfPs:sessionize<env></info>
EOT
            ,$this->getServiceUrl()));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $header_style = new OutputFormatterStyle('white', 'green', array('bold'));
        $style = new SymfonyStyle($input, $output);
        $output->getFormatter()->setStyle('header', $header_style);

        $start = new \DateTime($input->getOption('start'));

        if (! $start instanceof \DateTime) {
            throw new \InvalidArgumentException('The given date could not be parsed');
        }

        $config = parse_ini_file(__DIR__ . '/../../config/callingallpapers.ini');

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'token ' . $config['github.token'],
            ],
        ]);

        $resultKeeper = new ConsoleOutputResultKeeper($style);
        $writer = new ApiCfpWriter($config['event_api_url'], $config['event_api_token'], $client);
        $writer->setOutput($output);
        $writer->setResultKeeper($resultKeeper);

        $style->comment(sprintf('Starting to parse %s', $this->getParserName()));

        // Set CfP-Filters
        $filter = new FilterList();
        $filter->add(new FollowUriRedirect(['conferenceUri'], $client));
        $filter->add(new StripParamsFromUri(['conferenceUri']));
        $writer->setFilter($filter);

        $timezoneService = new TimezoneService($client, $config['timezonedb_token']);
        $geolocationService = new GeolocationService($client);

        // Parse Confs.tech
        $parser = $this->getParser(new ServiceContainer($timezoneService, $geolocationService, $client));
        $parser->parse($writer);

        $style->newLine(2);

        $items = [];
        foreach ($resultKeeper->getCreated() as $created) {
            $items[] = $created->getConference();
        }

        if ($items) {
            $style->writeln('Added these new Conferences:');
            $style->listing($items);
        }

        $items = [];
        foreach ($resultKeeper->getUpdated() as $updated) {
            $items[] = $updated->getConference();
        }

        if ($items) {
            $style->writeln('Updated these Conferences:');
            $style->listing($items);
        }

        $items = [];
        foreach ($resultKeeper->getFailed() as $failed) {
            $items[] = sprintf(
                '%1$s (%2$s)',
                $failed->getConference(),
                $failed->getMessage()
            );
        }

        foreach ($resultKeeper->getErrored() as $errored) {
            $items[] = sprintf(
                '%1$s (%2$s)',
                $errored->getConference(),
                $errored->getMessage()
            );
        }

        if ($items) {
            $style->writeln('There where issues with these conferences:');
            $style->listing($items);
        }
    }

    abstract protected function getParser(ServiceContainer $serviceContainer) : ParserInterface;

    abstract protected function getParserName() : string;

    abstract protected function getParserId() : string;

    abstract protected function getServiceUrl() : string;
}

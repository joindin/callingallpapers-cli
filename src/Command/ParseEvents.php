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

use Callingallpapers\Parser\JoindinCfpParser;
use Callingallpapers\Parser\Lanyrd\LanyrdCfpParser;
use Callingallpapers\Service\TimezoneService;
use Callingallpapers\Parser\PhpNetCfpParser;
use Callingallpapers\Writer\ApiCfpWriter;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParseEvents extends Command
{
    protected function configure()
    {
        $this->setName("parseCfPs")
             ->setDescription("Retrieve CfPs and parse them")
             ->setDefinition(array(
                 new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'What should be the first date to be taken into account?', ''),
             ))
             ->setHelp(<<<EOT
Get details about CfPs from different sources

Usage:

<info>callingallpapers parseCfPs 2015-02-23<env></info>

If you ommit the date the current date will be used instead
<info>callingallpapers parseCfPs<env></info>
EOT
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $header_style = new OutputFormatterStyle('white', 'green', array('bold'));
        $output->getFormatter()->setStyle('header', $header_style);

        $start = new \DateTime($input->getOption('start'));

        if (! $start instanceof \DateTime) {
            throw new \InvalidArgumentException('The given date could not be parsed');
        }

        $config = parse_ini_file(__DIR__ . '/../../config/callingallpapers.ini');
        $writer = new ApiCfpWriter($config['event_api_url'], $config['event_api_token']);
        $writer->setOutput($output);

        $parser = new LanyrdCfpParser(new TimezoneService(new Client(), $config['timezonedb_token']));
        $parser->parse($writer);

        $parser = new PhpNetCfpParser();
        $parser->parse($writer);

        $parser = new JoindinCfpParser();
        $parser->parse($writer);
    }
}

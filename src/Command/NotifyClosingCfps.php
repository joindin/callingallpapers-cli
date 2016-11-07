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

use Callingallpapers\Notification\NotificationList;
use Callingallpapers\Notification\TwitterNotifier;
use Callingallpapers\Reader\ApiCfpReader;
use Callingallpapers\Service\TwitterNotifierClientFactory;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyClosingCfps extends Command
{
    protected function configure()
    {
        $this->setName("notifyClosingCfps")
             ->setDescription("Notify about CfPs that close within 24 hours")
             ->setDefinition(array(
                 new InputOption('start', 's', InputOption::VALUE_OPTIONAL, 'What should be the first date to be taken into account?', ''),
             ))
             ->setHelp(<<<EOT
Notify about CfPs that are closing within 24 hours

Usage:

<info>callingallpapers notifyClosingCfp</info>
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
        $reader = new ApiCfpReader($config['event_api_url'], $config['event_api_token']);
        $cfps = $reader->getCfpsEndingWithinInterval(
            new \DateInterval('PT1H'),
            (new \DateTimeImmutable())->add(new \DateInterval('PT23H'))
        );

        $notifications = new NotificationList();
        $notifications->add(new TwitterNotifier(
            TwitterNotifierClientFactory::getClient($config)
        ));

        $notifications->notify($cfps);
    }
}

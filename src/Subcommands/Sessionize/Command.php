<?php

declare(strict_types = 1);

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
namespace Callingallpapers\Subcommands\Sessionize;

use Callingallpapers\Command\AbstractParseEvents;
use Callingallpapers\Entity\Cfp;
use Callingallpapers\Parser\ParserInterface;
use Callingallpapers\Service\ServiceContainer;
use Callingallpapers\Subcommands\Sessionize\Parser\EntryParser;
use Callingallpapers\Subcommands\Sessionize\Parser\Sessionize;

class Command extends AbstractParseEvents
{
    const NAME = 'Sessionize';

    protected function getParser(ServiceContainer $serviceContainer): ParserInterface
    {
        $parser = new EntryParser(new Cfp(), $serviceContainer);

        return new Sessionize($parser);
    }

    protected function getParserName(): string
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

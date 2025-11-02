<?php
/**
 * Copyright (c) 2016-2016} Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2016-2016 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     27.03.2016
 * @link      http://github.com/heiglandreas/callingallpapers
 */

namespace Callingallpapers\Writer;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NullOutput implements OutputInterface
{

    /**
     * Returns whether verbosity is debug (-vvv).
     *
     * @return bool true if verbosity is set to VERBOSITY_DEBUG, false otherwise
     */
    public function isDebug(): bool
    {
        return false;
    }

    /**
     * Sets output formatter.
     *
     * @param OutputFormatterInterface $formatter
     *
     * @api
     */
    public function setFormatter(OutputFormatterInterface $formatter): void
    {
        // TODO: Implement setFormatter() method.
    }

    /**
     * Returns whether verbosity is verbose (-v).
     *
     * @return bool true if verbosity is set to VERBOSITY_VERBOSE, false otherwise
     */
    public function isVerbose(): bool
    {
        return false;
    }

    /**
     * Returns whether verbosity is very verbose (-vv).
     *
     * @return bool true if verbosity is set to VERBOSITY_VERY_VERBOSE, false otherwise
     */
    public function isVeryVerbose(): bool
    {
        return false;
    }

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param bool         $newline  Whether to add a newline
     * @param int          $type     The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     * @api
     */
    public function write(
        $messages,
        $newline = false,
        $options = self::OUTPUT_NORMAL
    ): void {
        // TODO: Implement write() method.
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param int          $options  The type of output (one of the OUTPUT constants)
     *
     * @throws \InvalidArgumentException When unknown output type is given
     * @api
     */
    public function writeln($messages, $options = self::OUTPUT_NORMAL): void
    {
        // TODO: Implement writeln() method.
    }

    /**
     * Sets the verbosity of the output.
     *
     * @param int $level The level of verbosity (one of the VERBOSITY constants)
     *
     * @api
     */
    public function setVerbosity($level): void
    {
        // TODO: Implement setVerbosity() method.
    }

    /**
     * Gets the current verbosity of the output.
     *
     * @return int The current level of verbosity (one of the VERBOSITY constants)
     * @api
     */
    public function getVerbosity(): int
    {
        return 0;
    }

    /**
     * Sets the decorated flag.
     *
     * @param bool $decorated Whether to decorate the messages
     *
     * @api
     */
    public function setDecorated($decorated): void
    {
        // TODO: Implement setDecorated() method.
    }

    /**
     * Gets the decorated flag.
     *
     * @return bool true if the output will decorate messages, false otherwise
     * @api
     */
    public function isDecorated(): bool
    {
        return 0;
    }

    /**
     * Returns current output formatter instance.
     *
     * @api
     */
    public function getFormatter(): OutputFormatterInterface
    {
        return new class() implements OutputFormatterInterface {

            public function setDecorated(bool $decorated): void
            {
                // TODO: Implement setDecorated() method.
            }

            public function isDecorated(): bool
            {
                return true;
            }

            public function setStyle(string $name, OutputFormatterStyleInterface $style): void
            {
                // TODO: Implement setStyle() method.
            }

            public function hasStyle(string $name): bool
            {
                return true;
            }

            public function getStyle(string $name): OutputFormatterStyleInterface
            {
                return new class() implements OutputFormatterStyleInterface {

                    public function setForeground(?string $color): void
                    {
                        // TODO: Implement setForeground() method.
                    }

                    public function setBackground(?string $color): void
                    {
                        // TODO: Implement setBackground() method.
                    }

                    public function setOption(string $option): void
                    {
                        // TODO: Implement setOption() method.
                    }

                    public function unsetOption(string $option): void
                    {
                        // TODO: Implement unsetOption() method.
                    }

                    public function setOptions(array $options): void
                    {
                        // TODO: Implement setOptions() method.
                    }

                    public function apply(string $text): string
                    {
                        return $text;
                    }
                };
            }

            public function format(?string $message): ?string
            {
                return $message;
            }
        };
    }

    /**
     * Returns whether verbosity is quiet (-q).
     *
     * @return bool true if verbosity is set to VERBOSITY_QUIET, false otherwise
     */
    public function isQuiet(): bool
    {
        return true;
    }
}

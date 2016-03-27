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

use Callingallpapers\Entity\Cfp;
use Org_Heigl\IteratorTrait\IteratorTrait;
use Symfony\Component\Console\Output\OutputInterface;

class WriterList implements WriterInterface, \Iterator
{
    use IteratorTrait;

    /**
     * @var WriterInterface[]
     */
    protected $writer = [];

    protected $output;

    public function __construct()
    {
        $this->output = new NullOutput();
    }

    /**
     * Get the array the iterator shall iterate over.
     *
     * @return array
     */
    protected function & getIterableElement()
    {
        return $this->writer;
    }

    /**
     * @param Cfp $cfp
     *
     * @return String
     */
    public function write(Cfp $cfp)
    {
        foreach ($this->writer as $writer) {
            $writer->setOutput($this->output);
            $writer->write($cfp);
        }
    }

    /**
     * Add a writer
     *
     * @param WriterInterface $writer
     *
     * @return void
     */
    public function addWriter(Writer $writer)
    {
        $this->writer[] = $writer;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }
}

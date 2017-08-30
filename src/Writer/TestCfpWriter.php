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
namespace Callingallpapers\Writer;

use Callingallpapers\Entity\Cfp;
use Symfony\Component\Console\Output\OutputInterface;

class TestCfpWriter implements WriterInterface
{
    private $cfps;

    public function __construct()
    {
        $this->cfps = [];
    }

    public function write(Cfp $cfp, $source)
    {
        $this->cfps[] = $cfp;
    }

    public function setOutput(OutputInterface $output)
    {
        // Do nothing
    }

    public function count()
    {
        return count($this->cfps);
    }

    public function valid()
    {
        /** @var \Callingallpapers\Entity\Cfp $cfp */
        foreach ($this->cfps as $cfp) {
            if (! $cfp->uri) {
                return false;
            }

            if (! $cfp->conferenceName) {
                return false;
            }

            if (! $cfp->dateEnd) {
                return false;
            }
        }

        return true;
    }
}

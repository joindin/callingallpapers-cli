<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\ResultKeeper;

use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputResultKeeper extends ResultKeeper
{
    private $style;

    private $total;

    private $line;

    public function __construct(OutputInterface $style)
    {
        $this->style = $style;
        $this->total = 0;
        $this->line  = 0;
    }

    public function addError(Error $error) : void
    {
        parent::addError($error);
        $this->print('<fg=red>E</>');
    }

    public function addFailure(Failure $failure) : void
    {
        parent::addFailure($failure);
        $this->print('<fg=red>F</>');
    }

    public function addCreated(Created $created) : void
    {
        parent::addCreated($created);
        $this->print('<info>C</info>');
    }

    public function addUpdated(Updated $updated) : void
    {
        parent::addUpdated($updated);
        $this->print('<info>U</info>');
    }

    private function print(string $s) : void
    {
        $this->total++;
        $this->line++;

        $this->style->write($s);

        if ($this->line > 63) {
            $this->style->writeln(sprintf(
                '  %03d',
                $this->total
            ));
            $this->line = 0;
        }
    }
}

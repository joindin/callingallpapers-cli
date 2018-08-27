<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\ResultKeeper;

use Symfony\Component\Console\Output\OutputInterface;

class ResultKeeper
{
    private $errors = [];

    private $failures = [];

    private $updated = [];

    private $created = [];

    public function add(Result $result)
    {
        switch (get_class($result)) {
            case Error::class:
                $this->addError($result);
                break;
            case Failure::class:
                $this->addFailure($result);
                break;
            case Created::class:
                $this->addCreated($result);
                break;
            case Updated::class:
                $this->addUpdated($result);
                break;
            default:
        }
    }

    public function addError(Error $error) : void
    {
        $this->errors[] = $error;
    }

    public function addFailure(Failure $failure) : void
    {
        $this->failures[] = $failure;
    }

    public function addCreated(Created $created) : void
    {
        $this->created[] = $created;
    }
    public function addUpdated(Updated $updated) : void
    {
        $this->updated[] = $updated;
    }

    /**
     * @return \Callingallpapers\ResultKeeper\Created[]
     */
    public function getCreated() : array
    {
        return $this->created;
    }

    /**
     * @return \Callingallpapers\ResultKeeper\Updated[]
     */
    public function getUpdated() : array
    {
        return $this->updated;
    }

    /**
     * @return \Callingallpapers\ResultKeeper\Failure[]
     */
    public function getFailed() : array
    {
        return $this->failures;
    }

    /**
     * @return \Callingallpapers\ResultKeeper\Error[]
     */
    public function getErrored() : array
    {
        return $this->errors;
    }
}

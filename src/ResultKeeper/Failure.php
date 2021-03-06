<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\ResultKeeper;

use Throwable;

class Failure implements Result
{
    private $conference;

    private $throwable;

    public function __construct(string $conference, Throwable $throwable)
    {
        $this->conference = $conference;
        $this->throwable  = $throwable;
    }

    public function getMessage() : string
    {
        return $this->throwable->getMessage();
    }

    public function getThrowable() : Throwable
    {
        return $this->throwable;
    }

    public function getConference(): string
    {
        return $this->conference;
    }
}

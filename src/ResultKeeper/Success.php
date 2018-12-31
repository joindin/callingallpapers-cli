<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\ResultKeeper;

abstract class Success implements Result
{
    private $conference;

    public function __construct(string $conference)
    {
        $this->conference = $conference;
    }

    public function getConference(): string
    {
        return $this->conference;
    }
}

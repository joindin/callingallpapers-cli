<?php

/*
 * Copyright (c) Andreas Heigl<andreas@heigl.org
 *
 * Licensed under the MIT License. See LICENSE.md file in the project root
 * for full license information.
 */

namespace Callingallpapers\Parser;

use Callingallpapers\Entity\CfpList;
use Callingallpapers\Service\ConfsTechParserFactory;
use Callingallpapers\Writer\WriterInterface;

class ConfsTechParser implements ParserInterface
{
    private $factory;

    public function __construct(
        ConfsTechParserFactory $factory
    ) {
        $this->factory = $factory;
    }

    /**
     * @param WriterInterface $output
     *
     * @return CfpList
     */
    public function parse(WriterInterface $output)
    {
        $parser = $this->factory->createParser($output);

        $parser();
    }
}

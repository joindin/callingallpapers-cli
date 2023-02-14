<?php

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licensed under the MIT-license. For details see the included file LICENSE.md
 */
declare(strict_types=1);

namespace Callingallpapers\Service;

use function parse_ini_file;

class ConfigService
{
    private $configFile = __DIR__ . '/../../config/callingallpapers.ini';

    public function __construct() {}

    public function getConfiguration(): array
    {
        return parse_ini_file($this->configFile)??[];
    }
}
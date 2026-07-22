<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model\Handler;

use FishPig\CspReport\Model\Config;

class DomainExcluder
{
    public function __construct(
        private Config $config
    ) {}

    public function isExcluded(string $blockedUri): bool
    {
        $host = strtolower((string)parse_url($blockedUri, PHP_URL_HOST));

        if ($host === '') {
            return false;
        }

        foreach ($this->config->getExcludedDomains() as $pattern) {
            if (fnmatch(strtolower($pattern), $host)) {
                return true;
            }
        }

        return false;
    }
}

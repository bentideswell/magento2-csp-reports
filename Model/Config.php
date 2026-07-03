<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'fishpig_cspreport/general/enabled';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {}

    public function isEnabled(string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, $scopeType, $scopeCode);
    }
}

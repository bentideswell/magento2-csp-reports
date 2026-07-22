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
    private const XML_PATH_EXCLUDED_DOMAINS = 'fishpig_cspreport/general/excluded_domains';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {}

    public function isEnabled(string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, $scopeType, $scopeCode);
    }

    /**
     * @return string[] Unique, non-empty, lowercase domain patterns (may contain * wildcards)
     */
    public function getExcludedDomains(string $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null): array
    {
        $value = (string)$this->scopeConfig->getValue(self::XML_PATH_EXCLUDED_DOMAINS, $scopeType, $scopeCode);

        $domains = array_map(
            static fn (string $domain): string => strtolower(trim($domain)),
            preg_split('/[\r\n]+/', $value) ?: []
        );

        return array_values(array_unique(array_filter(
            $domains,
            static fn (string $domain): bool => $domain !== ''
        )));
    }
}

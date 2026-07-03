<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Plugin\Csp;

use FishPig\CspReport\Model\Config;
use Magento\Csp\Api\Data\ModeConfiguredInterface;
use Magento\Csp\Api\ModeConfigManagerInterface;
use Magento\Csp\Model\Mode\Data\ModeConfiguredFactory;
use Magento\Store\Model\StoreManagerInterface;

class ModeConfigPlugin
{
    public function __construct(
        private Config $config,
        private ModeConfiguredFactory $modeConfiguredFactory,
        private StoreManagerInterface $storeManager
    ) {}

    public function afterGetConfigured(
        ModeConfigManagerInterface $subject,
        ModeConfiguredInterface $result
    ): ModeConfiguredInterface {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        try {
            $baseUrl = rtrim((string)$this->storeManager->getStore()->getBaseUrl(), '/');
            $collectUrl = $baseUrl . '/fishpig_cspreport/report/collect';
        } catch (\Throwable) {
            return $result;
        }

        return $this->modeConfiguredFactory->create([
            'reportOnly' => $result->isReportOnly(),
            'reportUri'  => $collectUrl,
        ]);
    }
}

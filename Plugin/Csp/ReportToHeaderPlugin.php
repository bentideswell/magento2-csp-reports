<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Plugin\Csp;

use FishPig\CspReport\Model\Config;
use Magento\Framework\App\Response\Http as HttpResponse;

class ReportToHeaderPlugin
{
    public function __construct(
        private Config $config
    ) {}

    /**
     * Browsers that recognise the "report-to" directive stop sending "report-uri" reports
     * entirely once it's present, even if the report-to endpoint (registered via the legacy
     * "Report-To" header Magento core sends) never actually receives anything. Strip it so the
     * still-working report-uri delivery isn't silently suppressed.
     */
    public function beforeSendResponse(HttpResponse $subject): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $subject->clearHeader('Report-To');

        foreach (['Content-Security-Policy', 'Content-Security-Policy-Report-Only'] as $headerName) {
            if ($header = $subject->getHeader($headerName)) {
                $value = preg_replace('/\s*report-to\s+[^;]+;?/i', '', $header->getFieldValue());
                $subject->setHeader($headerName, $value, true);
            }
        }
    }
}

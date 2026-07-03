<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Controller\Report;

use FishPig\CspReport\Api\CspReportHandlerInterface;
use FishPig\CspReport\Model\Config;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Collect implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private RequestInterface $request,
        private ResultFactory $resultFactory,
        private CspReportHandlerInterface $handler,
        private Config $config
    ) {}

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setHttpResponseCode(204);
        $result->setContents('');

        if (!$this->config->isEnabled()) {
            return $result;
        }

        try {
            $body = $this->request->getContent();
            if ($body !== '' && $body !== null) {
                $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
                // Reporting API (report-to) sends an array; legacy report-uri sends a single object
                if (array_is_list($payload)) {
                    foreach ($payload as $report) {
                        if (($report['type'] ?? '') === 'csp-violation') {
                            $this->handler->handle($report);
                        }
                    }
                } else {
                    $this->handler->handle($payload);
                }
            }
        } catch (\Throwable) {
            // Always return 204 — browser should not retry on error responses
        }

        return $result;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}

<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model\Handler;

use FishPig\CspReport\Api\CspReportHandlerInterface;
use FishPig\CspReport\Model\ReportFactory;
use FishPig\CspReport\Model\ResourceModel\Report as ReportResource;
use FishPig\CspReport\Model\ResourceModel\Violation as ViolationResource;
use FishPig\CspReport\Model\ViolationFactory;
use Psr\Log\LoggerInterface;

class ReportUriHandler implements CspReportHandlerInterface
{
    public function __construct(
        private ViolationFactory $violationFactory,
        private ViolationResource $violationResource,
        private ReportFactory $reportFactory,
        private ReportResource $reportResource,
        private DomainExcluder $domainExcluder,
        private LoggerInterface $logger
    ) {}

    public function handle(array $payload): void
    {
        try {
            $data = $this->normalise($payload);

            $blockedUri        = (string)($data['blocked_uri'] ?? '');
            $violatedDirective = (string)($data['violated_directive'] ?? '');
            $documentUri       = (string)($data['document_uri'] ?? '');

            if ($blockedUri === '' && $violatedDirective === '') {
                return;
            }

            // Browser extensions injecting resources into the page, not the site's own violations
            if (preg_match('#^(?:chrome|moz|safari|ms-browser)-extension://#', $blockedUri)) {
                return;
            }

            if ($this->domainExcluder->isExcluded($blockedUri)) {
                return;
            }

            $violation = $this->violationFactory->create();
            $this->violationResource->loadByFingerprint($violation, $blockedUri, $violatedDirective);

            if (!$violation->getId()) {
                $violation->setData([
                    'blocked_uri'         => $blockedUri,
                    'violated_directive'  => $violatedDirective,
                    'effective_directive' => (string)($data['effective_directive'] ?? ''),
                    'referrer'            => (string)($data['referrer'] ?? ''),
                    'original_policy'     => isset($data['original_policy']) ? (string)$data['original_policy'] : null,
                    'disposition'         => (string)($data['disposition'] ?? 'enforce'),
                    'status_code'         => isset($data['status_code']) ? (int)$data['status_code'] : null,
                    'script_sample'       => isset($data['script_sample']) ? (string)$data['script_sample'] : null,
                    'source_file'         => (string)($data['source_file'] ?? ''),
                    'line_number'         => isset($data['line_number']) ? (int)$data['line_number'] : null,
                    'column_number'       => isset($data['column_number']) ? (int)$data['column_number'] : null,
                    'report_count'        => 0,
                ]);
                $this->violationResource->save($violation);
            }

            $report = $this->reportFactory->create();
            $report->setData('violation_id', $violation->getId());
            $report->setData('document_uri', $documentUri);
            $this->reportResource->save($report);

            $this->violationResource->incrementReportCount((int)$violation->getId());
        } catch (\Throwable $e) {
            $this->logger->error('FishPig_CspReport: failed to store report - ' . $e->getMessage());
        }
    }

    /**
     * Normalise both legacy report-uri format and Reporting API (report-to) format to a
     * flat snake_case array.
     *
     * Legacy:   {"csp-report": {"blocked-uri": "...", ...}}
     * Reporting API: {"type": "csp-violation", "url": "...", "body": {"blockedURL": "...", ...}}
     */
    private function normalise(array $payload): array
    {
        // Reporting API format: payload has a "body" key with camelCase fields
        if (isset($payload['body']) && is_array($payload['body'])) {
            $body = $payload['body'];
            return [
                'blocked_uri'         => $body['blockedURL'] ?? '',
                'violated_directive'  => $body['violatedDirective'] ?? $body['effectiveDirective'] ?? '',
                'effective_directive' => $body['effectiveDirective'] ?? '',
                'document_uri'        => $body['documentURL'] ?? $payload['url'] ?? '',
                'referrer'            => $body['referrer'] ?? '',
                'original_policy'     => $body['originalPolicy'] ?? null,
                'disposition'         => $body['disposition'] ?? 'enforce',
                'status_code'         => isset($body['statusCode']) ? (int)$body['statusCode'] : null,
                'script_sample'       => $body['sample'] ?? null,
                'source_file'         => $body['sourceFile'] ?? '',
                'line_number'         => isset($body['lineNumber']) ? (int)$body['lineNumber'] : null,
                'column_number'       => isset($body['columnNumber']) ? (int)$body['columnNumber'] : null,
            ];
        }

        // Legacy report-uri format: {"csp-report": {...}} or bare {...}
        $data = $payload['csp-report'] ?? $payload;
        return [
            'blocked_uri'         => $data['blocked-uri'] ?? '',
            'violated_directive'  => $data['violated-directive'] ?? '',
            'effective_directive' => $data['effective-directive'] ?? '',
            'document_uri'        => $data['document-uri'] ?? '',
            'referrer'            => $data['referrer'] ?? '',
            'original_policy'     => $data['original-policy'] ?? null,
            'disposition'         => $data['disposition'] ?? 'enforce',
            'status_code'         => isset($data['status-code']) ? (int)$data['status-code'] : null,
            'script_sample'       => $data['script-sample'] ?? null,
            'source_file'         => $data['source-file'] ?? '',
            'line_number'         => isset($data['line-number']) ? (int)$data['line-number'] : null,
            'column_number'       => isset($data['column-number']) ? (int)$data['column-number'] : null,
        ];
    }
}

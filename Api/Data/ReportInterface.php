<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Api\Data;

interface ReportInterface
{
    public const REPORT_ID = 'report_id';
    public const VIOLATION_ID = 'violation_id';
    public const DOCUMENT_URI = 'document_uri';
    public const REPORTED_AT = 'reported_at';

    public function getId(): ?int;
    public function getViolationId(): int;
    public function getDocumentUri(): string;
    public function getReportedAt(): string;
}

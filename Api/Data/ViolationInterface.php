<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Api\Data;

interface ViolationInterface
{
    public const VIOLATION_ID = 'violation_id';
    public const BLOCKED_URI = 'blocked_uri';
    public const VIOLATED_DIRECTIVE = 'violated_directive';
    public const EFFECTIVE_DIRECTIVE = 'effective_directive';
    public const REFERRER = 'referrer';
    public const ORIGINAL_POLICY = 'original_policy';
    public const DISPOSITION = 'disposition';
    public const STATUS_CODE = 'status_code';
    public const SCRIPT_SAMPLE = 'script_sample';
    public const SOURCE_FILE = 'source_file';
    public const LINE_NUMBER = 'line_number';
    public const COLUMN_NUMBER = 'column_number';
    public const REPORT_COUNT = 'report_count';
    public const LAST_REPORTED_AT = 'last_reported_at';

    public function getId(): ?int;
    public function getBlockedUri(): string;
    public function getViolatedDirective(): string;
    public function getEffectiveDirective(): string;
    public function getReferrer(): string;
    public function getOriginalPolicy(): ?string;
    public function getDisposition(): string;
    public function getStatusCode(): ?int;
    public function getScriptSample(): ?string;
    public function getSourceFile(): string;
    public function getLineNumber(): ?int;
    public function getColumnNumber(): ?int;
    public function getReportCount(): int;
    public function getLastReportedAt(): ?string;
}

<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model;

use FishPig\CspReport\Api\Data\ViolationInterface;
use Magento\Framework\Model\AbstractModel;

class Violation extends AbstractModel implements ViolationInterface
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Violation::class);
    }

    public function getId(): ?int
    {
        return $this->getData(self::VIOLATION_ID) ? (int)$this->getData(self::VIOLATION_ID) : null;
    }

    public function getBlockedUri(): string
    {
        return (string)$this->getData(self::BLOCKED_URI);
    }

    public function getViolatedDirective(): string
    {
        return (string)$this->getData(self::VIOLATED_DIRECTIVE);
    }

    public function getEffectiveDirective(): string
    {
        return (string)$this->getData(self::EFFECTIVE_DIRECTIVE);
    }

    public function getReferrer(): string
    {
        return (string)$this->getData(self::REFERRER);
    }

    public function getOriginalPolicy(): ?string
    {
        return $this->getData(self::ORIGINAL_POLICY);
    }

    public function getDisposition(): string
    {
        return (string)$this->getData(self::DISPOSITION);
    }

    public function getStatusCode(): ?int
    {
        return $this->getData(self::STATUS_CODE) !== null ? (int)$this->getData(self::STATUS_CODE) : null;
    }

    public function getScriptSample(): ?string
    {
        return $this->getData(self::SCRIPT_SAMPLE);
    }

    public function getSourceFile(): string
    {
        return (string)$this->getData(self::SOURCE_FILE);
    }

    public function getLineNumber(): ?int
    {
        return $this->getData(self::LINE_NUMBER) !== null ? (int)$this->getData(self::LINE_NUMBER) : null;
    }

    public function getColumnNumber(): ?int
    {
        return $this->getData(self::COLUMN_NUMBER) !== null ? (int)$this->getData(self::COLUMN_NUMBER) : null;
    }

    public function getReportCount(): int
    {
        return (int)$this->getData(self::REPORT_COUNT);
    }

    public function getLastReportedAt(): ?string
    {
        return $this->getData(self::LAST_REPORTED_AT);
    }
}

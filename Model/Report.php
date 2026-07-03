<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model;

use FishPig\CspReport\Api\Data\ReportInterface;
use Magento\Framework\Model\AbstractModel;

class Report extends AbstractModel implements ReportInterface
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Report::class);
    }

    public function getId(): ?int
    {
        return $this->getData(self::REPORT_ID) ? (int)$this->getData(self::REPORT_ID) : null;
    }

    public function getViolationId(): int
    {
        return (int)$this->getData(self::VIOLATION_ID);
    }

    public function getDocumentUri(): string
    {
        return (string)$this->getData(self::DOCUMENT_URI);
    }

    public function getReportedAt(): string
    {
        return (string)$this->getData(self::REPORTED_AT);
    }
}

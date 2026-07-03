<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Violation extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('fishpig_csp_violation', 'violation_id');
    }

    /**
     * Find a violation by its unique fingerprint (blocked_uri + violated_directive).
     */
    public function loadByFingerprint(
        \FishPig\CspReport\Model\Violation $violation,
        string $blockedUri,
        string $violatedDirective
    ): self {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('blocked_uri = ?', $blockedUri)
            ->where('violated_directive = ?', $violatedDirective)
            ->limit(1);

        $data = $connection->fetchRow($select);
        if ($data) {
            $violation->setData($data);
            $this->_afterLoad($violation);
        }

        return $this;
    }

    /**
     * Increment report_count and update last_reported_at.
     */
    public function incrementReportCount(int $violationId): void
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            [
                'report_count' => new \Zend_Db_Expr('report_count + 1'),
                'last_reported_at' => new \Zend_Db_Expr('NOW()')
            ],
            ['violation_id = ?' => $violationId]
        );
    }

    /**
     * Recalculate report_count and last_reported_at from the reports table after a report is deleted.
     */
    public function recalculateStats(int $violationId): void
    {
        $connection = $this->getConnection();
        $reportTable = $this->getTable('fishpig_csp_report');

        $select = $connection->select()
            ->from($reportTable, ['count' => new \Zend_Db_Expr('COUNT(*)'), 'last' => new \Zend_Db_Expr('MAX(reported_at)')])
            ->where('violation_id = ?', $violationId);

        $row = $connection->fetchRow($select);

        $connection->update(
            $this->getMainTable(),
            [
                'report_count' => (int)($row['count'] ?? 0),
                'last_reported_at' => $row['last'] ?? null
            ],
            ['violation_id = ?' => $violationId]
        );
    }
}

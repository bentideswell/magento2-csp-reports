<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Controller\Adminhtml\Report;

use FishPig\CspReport\Model\ReportFactory;
use FishPig\CspReport\Model\ResourceModel\Report as ReportResource;
use FishPig\CspReport\Model\ResourceModel\Violation as ViolationResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class DeleteOne extends Action
{
    public const ADMIN_RESOURCE = 'FishPig_CspReport::violations';

    public function __construct(
        Context $context,
        private ReportFactory $reportFactory,
        private ReportResource $reportResource,
        private ViolationResource $violationResource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $violationId = (int)$this->getRequest()->getParam('violation_id', 0);

        try {
            $reportId = (int)$this->getRequest()->getParam('report_id', 0);
            $report = $this->reportFactory->create()->load($reportId);

            if (!$report->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Report not found.'));
            }

            $violationId = $report->getViolationId();
            $this->reportResource->delete($report);
            $this->violationResource->recalculateStats($violationId);
            $this->messageManager->addSuccessMessage(__('Report occurrence deleted.'));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($violationId) {
            return $redirect->setPath('fishpig_cspreport/violation/view', ['violation_id' => $violationId]);
        }

        return $redirect->setPath('fishpig_cspreport/violation/index');
    }
}

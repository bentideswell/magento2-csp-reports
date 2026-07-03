<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Controller\Adminhtml\Violation;

use FishPig\CspReport\Model\ViolationRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class DeleteOne extends Action
{
    public const ADMIN_RESOURCE = 'FishPig_CspReport::violations';

    public function __construct(
        Context $context,
        private ViolationRepository $violationRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $violation = $this->violationRepository->getById(
                (int)$this->getRequest()->getParam('violation_id', 0)
            );
            $this->violationRepository->delete($violation);
            $this->messageManager->addSuccessMessage(__('Violation deleted.'));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('*/*/index');
    }
}

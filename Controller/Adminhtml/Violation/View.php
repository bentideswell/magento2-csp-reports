<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Controller\Adminhtml\Violation;

use FishPig\CspReport\Model\Adminhtml\ViolationLocator;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;

class View implements ActionInterface
{
    public const ADMIN_RESOURCE = 'FishPig_CspReport::violations';

    public function __construct(
        private ResultFactory $resultFactory,
        private ViolationLocator $violationLocator,
        private ManagerInterface $messageManager
    ) {}

    public function execute()
    {
        try {
            $violation = $this->violationLocator->get();

            /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->setActiveMenu('FishPig_CspReport::violations');
            $resultPage->getConfig()->getTitle()->prepend(
                __('CSP Violations / %1', $violation->getBlockedUri() ?: $violation->getViolatedDirective())
            );
            return $resultPage;
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $redirect */
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $redirect->setPath('fishpig_cspreport/violation/index');
        }
    }
}

<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Controller\Adminhtml\Violation;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Index implements ActionInterface
{
    public const ADMIN_RESOURCE = 'FishPig_CspReport::violations';

    public function __construct(
        private ResultFactory $resultFactory
    ) {}

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('FishPig_CspReport::violations');
        $resultPage->getConfig()->getTitle()->prepend(__('CSP Violations'));
        return $resultPage;
    }
}

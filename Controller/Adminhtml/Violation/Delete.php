<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Controller\Adminhtml\Violation;

use FishPig\CspReport\Model\ResourceModel\Violation\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'FishPig_CspReport::violations';

    public function __construct(
        Context $context,
        private Filter $massActionFilter,
        private CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $violations = $this->massActionFilter->getCollection(
                $this->collectionFactory->create()
            )->getItems();

            foreach ($violations as $violation) {
                $violation->delete();
            }

            $this->messageManager->addSuccessMessage(__('Deleted %1 violation(s).', count($violations)));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('*/*/index');
    }
}

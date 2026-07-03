<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Block\Adminhtml\Violation\View\Button;

use FishPig\CspReport\Model\Adminhtml\ViolationLocator;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{
    public function __construct(
        private Context $context,
        private ViolationLocator $violationLocator
    ) {}

    public function getButtonData(): array
    {
        $violation = $this->violationLocator->get();

        if ($violation === null) {
            return [];
        }

        return [
            'label'      => __('Delete Violation'),
            'class'      => 'delete',
            'sort_order' => 20,
            'on_click'   => sprintf(
                "deleteConfirm('%s', '%s', {data: {}})",
                __('Are you sure you want to delete this violation and all its reports?'),
                $this->context->getUrl(
                    'fishpig_cspreport/violation/deleteOne',
                    ['violation_id' => $violation->getId()]
                )
            ),
        ];
    }
}

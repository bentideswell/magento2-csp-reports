<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ViolationActions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['violation_id'])) {
                continue;
            }

            $name = $this->getData('name');
            $item[$name]['view'] = [
                'href'  => $this->urlBuilder->getUrl('fishpig_cspreport/violation/view', ['violation_id' => $item['violation_id']]),
                'label' => (string)__('View'),
            ];
            $item[$name]['delete'] = [
                'href'    => $this->urlBuilder->getUrl('fishpig_cspreport/violation/deleteOne', ['violation_id' => $item['violation_id']]),
                'label'   => (string)__('Delete'),
                'confirm' => [
                    'title'   => (string)__('Delete Violation'),
                    'message' => (string)__('Are you sure you want to delete this violation and all its reports?'),
                ],
            ];
        }

        return $dataSource;
    }
}

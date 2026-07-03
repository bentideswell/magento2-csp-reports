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

class ReportActions extends Column
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
            if (!isset($item['report_id'])) {
                continue;
            }

            $name = $this->getData('name');
            $item[$name]['delete'] = [
                'href'    => $this->urlBuilder->getUrl(
                    'fishpig_cspreport/report/deleteOne',
                    [
                        'report_id'    => $item['report_id'],
                        'violation_id' => $item['violation_id'] ?? 0,
                    ]
                ),
                'label'   => (string)__('Delete'),
                'confirm' => [
                    'title'   => (string)__('Delete Occurrence'),
                    'message' => (string)__('Are you sure you want to delete this report occurrence?'),
                ],
            ];
        }

        return $dataSource;
    }
}

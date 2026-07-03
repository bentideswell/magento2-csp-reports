<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Ui\DataProvider\Violation\Form;

use FishPig\CspReport\Model\ResourceModel\Violation\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    private ?array $loadedData = null;
    private ?array $loadedMeta = null;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        private \Magento\Framework\App\RequestInterface $request,
        private \Magento\Framework\UrlInterface $urlBuilder,
        array $meta = [],
        array $data = [],
        ?PoolInterface $pool = null
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        foreach ($this->collection->getItems() as $violation) {
            $this->loadedData[$violation->getId()] = $violation->getData();
        }

        return $this->loadedData;
    }

    public function getMeta(): array
    {
        if ($this->loadedMeta !== null) {
            return $this->loadedMeta;
        }

        $meta = parent::getMeta();
        $meta = $this->addListingComponent($meta, 'fishpig_cspreport_report_listing', (string)__('Report Occurrences'));
        $this->loadedMeta = $meta;

        return $this->loadedMeta;
    }

    private function addListingComponent(array $meta, string $listingTarget, string $label): array
    {
        return array_replace_recursive(
            $meta,
            [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'         => $label,
                                'collapsible'   => true,
                                'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                            ],
                        ],
                    ],
                    'children' => [
                        'listing' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'autoRender'       => true,
                                        'componentType'    => 'insertListing',
                                        'dataScope'        => $listingTarget,
                                        'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                        'ns'               => $listingTarget,
                                        'render_url'       => $this->getChildRenderUrl(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    private function getChildRenderUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'mui/index/render',
            [
                '_query' => [
                    $this->getRequestFieldName() => $this->getCurrentViolationId(),
                    'context'                    => 'violation',
                ],
            ]
        );
    }

    private function getCurrentViolationId(): ?int
    {
        return (int)$this->request->getParam($this->getRequestFieldName()) ?: null;
    }
}

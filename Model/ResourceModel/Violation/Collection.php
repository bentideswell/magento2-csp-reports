<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model\ResourceModel\Violation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'violation_id';

    protected function _construct(): void
    {
        $this->_init(
            \FishPig\CspReport\Model\Violation::class,
            \FishPig\CspReport\Model\ResourceModel\Violation::class
        );
    }
}

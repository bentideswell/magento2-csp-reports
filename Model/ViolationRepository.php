<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class ViolationRepository
{
    private array $cache = [];

    public function __construct(
        private ViolationFactory $violationFactory,
        private ResourceModel\Violation $resource
    ) {}

    public function getById(int $id): Violation
    {
        if (!isset($this->cache[$id])) {
            $violation = $this->violationFactory->create()->load($id);

            if (!$violation->getId()) {
                throw new NoSuchEntityException(__('CSP violation with ID "%1" does not exist.', $id));
            }

            $this->cache[$id] = $violation;
        }

        return $this->cache[$id];
    }

    public function save(Violation $violation): void
    {
        $this->resource->save($violation);
        $this->cache[$violation->getId()] = $violation;
    }

    public function delete(Violation $violation): void
    {
        $id = $violation->getId();
        $this->resource->delete($violation);
        unset($this->cache[$id]);
    }
}

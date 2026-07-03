<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Model\Adminhtml;

use FishPig\CspReport\Model\Violation;
use FishPig\CspReport\Model\ViolationRepository;
use Magento\Framework\App\RequestInterface;

class ViolationLocator
{
    private ?Violation $violation = null;

    public function __construct(
        private ViolationRepository $violationRepository,
        private RequestInterface $request
    ) {}

    public function get(): ?Violation
    {
        if ($this->violation === null) {
            $this->violation = $this->violationRepository->getById(
                (int)$this->request->getParam('violation_id')
            );
        }

        return $this->violation;
    }
}

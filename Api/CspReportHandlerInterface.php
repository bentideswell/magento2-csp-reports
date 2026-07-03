<?php
/**
 * Copyright © FishPig, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace FishPig\CspReport\Api;

interface CspReportHandlerInterface
{
    /**
     * Handle an incoming CSP report payload.
     *
     * @param array $payload Raw decoded JSON payload from the browser report
     */
    public function handle(array $payload): void;
}

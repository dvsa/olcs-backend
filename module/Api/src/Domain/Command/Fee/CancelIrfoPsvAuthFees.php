<?php

/**
 * Cancel Irfo Psv Auth Fees
 */
namespace Dvsa\Olcs\Api\Domain\Command\Fee;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Cancel Irfo Psv Auth Fees
 */
final class CancelIrfoPsvAuthFees extends AbstractIdOnlyCommand
{
    protected $exclusions;

    /**
     * @return mixed
     */
    public function getExclusions()
    {
        return $this->exclusions;
    }
}

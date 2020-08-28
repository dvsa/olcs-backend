<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

class NoOfPermitsFactory
{
    /**
     * Create and return a NoOfPermits instance
     *
     * @param int $maxCanApplyFor
     * @param int $maxPermitted
     * @param int $applicationFee
     * @param int $issueFee
     *
     * @return NoOfPermits
     */
    public function create($maxCanApplyFor, $maxPermitted, $applicationFee, $issueFee)
    {
        return new NoOfPermits($maxCanApplyFor, $maxPermitted, $applicationFee, $issueFee);
    }
}

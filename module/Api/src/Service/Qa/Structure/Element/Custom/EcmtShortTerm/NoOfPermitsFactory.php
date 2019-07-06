<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

class NoOfPermitsFactory
{
    /**
     * Create and return a NoOfPermits instance
     *
     * @param int $year
     * @param int $maxPermitted
     *
     * @return NoOfPermits
     */
    public function create($year, $maxPermitted)
    {
        return new NoOfPermits($year, $maxPermitted);
    }
}

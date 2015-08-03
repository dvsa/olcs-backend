<?php

/**
 * Create Application Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Create Application Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateApplicationFee extends AbstractIdOnlyCommand
{
    protected $feeTypeFeeType;

    /**
     * @return mixed
     */
    public function getFeeTypeFeeType()
    {
        return $this->feeTypeFeeType;
    }
}

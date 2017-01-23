<?php

namespace Dvsa\Olcs\Api\Domain\Command\ConditionUndertaking;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create small vehicle condition
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateSmallVehicleCondition extends AbstractCommand
{
    protected $applicationId;

    /**
     * @return mixed
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }
}

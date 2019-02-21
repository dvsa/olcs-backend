<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Entity\Surrender;

trait SurrenderStatusAwareTrait
{
    protected function hasBeenSubmitted(Surrender $surrender)
    {
        return $surrender->getStatus()->getId() === Surrender::SURRENDER_STATUS_SUBMITTED;
    }
}

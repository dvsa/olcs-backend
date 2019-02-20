<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Entity\Surrender;

trait SurrenderStatusAwareTrait
{
    protected function hasNotBeenSubmitted(Surrender $surrender)
    {
        return $surrender->getStatus() !== Surrender::SURRENDER_STATUS_SUBMITTED;
    }
}

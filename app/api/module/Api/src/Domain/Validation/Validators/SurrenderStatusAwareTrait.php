<?php


namespace Dvsa\Olcs\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Entity\Surrender;

trait SurrenderStatusAwareTrait
{
    /**
     * @param array $surrender
     *
     * @return bool
     */
    protected function hasBeenSigned(array $surrender)
    {
        return $surrender['status']['id'] === Surrender::SURRENDER_STATUS_SIGNED;
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Delete TMA
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Delete extends AbstractHandler
{
    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        foreach ($dto->getIds() as $id) {
            if ($this->canAccessTransportManagerApplication($id) !== true) {
                return false;
            }
        }

        return true;
    }
}

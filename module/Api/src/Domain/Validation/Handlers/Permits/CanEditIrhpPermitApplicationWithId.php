<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Can edit an irhp permit app (irhpPermitApplication Entity) with id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CanEditIrhpPermitApplicationWithId extends AbstractHandler
{
    /**
     * Whether the user can edit the IRHP permit application
     *
     * @param CommandInterface|QueryInterface $dto transfer object
     *
     * @return bool
     */
    public function isValid($dto)
    {
        return $this->canEditIrhpPermitApplicationWithId($dto->getId());
    }
}

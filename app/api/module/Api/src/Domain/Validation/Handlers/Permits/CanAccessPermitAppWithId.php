<?php

namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\Permits;

use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Can access a permit app with id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CanAccessPermitAppWithId extends AbstractHandler
{

    /**
     * Whether the user can access the permit application
     *
     * @param CommandInterface|QueryInterface $dto transfer object
     *
     * @return bool
     */
    public function isValid($dto)
    {
        return $this->canAccessPermitApp($dto->getId());
    }
}

<?php

/**
 * Delete
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\People\Application;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Delete
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Delete extends AbstractHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        $applicationId = $dto->getId();

        // If the user is external, they must own the application
        if ($this->isExternalUser() && $this->doesOwnApplication($applicationId) === false) {
            return false;
        }

        $personIds = $dto->getPersonIds();

        foreach ($personIds as $personId) {

        }
    }
}

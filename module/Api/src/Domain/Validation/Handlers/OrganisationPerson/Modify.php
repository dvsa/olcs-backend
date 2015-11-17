<?php

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Modify extends AbstractHandler implements RepositoryManagerAwareInterface, AuthAwareInterface
{
    use RepositoryManagerAwareTrait,
        AuthAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        if ($this->isExternalUser()) {
            return false;
        }

        $ids = $this->getIds($dto);

        foreach ($ids as $id) {
            if ($this->canAccessOrganisationPerson($id) === false) {
                return false;
            }
        }

        return true;
    }

    protected function getIds($dto)
    {
        return $dto->getIds();
    }
}

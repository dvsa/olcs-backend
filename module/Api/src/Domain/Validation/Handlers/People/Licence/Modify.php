<?php

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\People\Licence;

use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Person\Person;

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Modify extends AbstractHandler implements RepositoryManagerAwareInterface
{
    use RepositoryManagerAwareTrait;

    /**
     * @inheritdoc
     */
    public function isValid($dto)
    {
        $licenceId = $dto->getId();

        // If the user can't access the application
        if ($this->canAccessLicence($licenceId) === false) {
            return false;
        }

        $licence = $this->getRepo('Licence')->fetchById($licenceId);

        $people = $this->getPeople($dto);

        // Check that the people belong to the application
        foreach ($people as $person) {
            if ($this->doesPersonBelongToOrg($person, $licence) === false) {
                return false;
            }
        }

        return true;
    }

    protected function getPeople($dto)
    {
        return $this->getRepo('Person')->fetchByIds($dto->getPersonIds());
    }

    protected function doesPersonBelongToOrg(Person $person, Licence $licence)
    {
        $results = $this->getRepo('OrganisationPerson')
            ->fetchByOrgAndPerson($licence->getRelatedOrganisation(), $person);

        return !empty($results);
    }
}

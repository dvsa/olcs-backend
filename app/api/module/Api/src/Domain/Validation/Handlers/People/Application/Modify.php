<?php

/**
 * Modify
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Validation\Handlers\People\Application;

use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareInterface;
use Dvsa\Olcs\Api\Domain\RepositoryManagerAwareTrait;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\AbstractHandler;
use Dvsa\Olcs\Api\Entity\Application\Application;
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
        $applicationId = $dto->getId();

        // If the user can't access the application
        if ($this->canAccessApplication($applicationId) === false) {
            return false;
        }

        $application = $this->getRepo('Application')->fetchById($applicationId);

        $people = $this->getPeople($dto);

        // Check that the people belong to the application
        foreach ($people as $person) {
            if ($this->doesPersonBelongToApplicationOrOrg($person, $application) === false) {
                return false;
            }
        }

        return true;
    }

    protected function getPeople($dto)
    {
        return $this->getRepo('Person')->fetchByIds($dto->getPersonIds());
    }

    protected function doesPersonBelongToApplicationOrOrg(Person $person, Application $application)
    {
        try {
            $this->getRepo('ApplicationOrganisationPerson')
                ->fetchForApplicationAndPerson($application->getId(), $person->getId());

            return true;
        } catch (\Dvsa\Olcs\Api\Domain\Exception\NotFoundException $e) {
            // Repo thrown a not found exception, so we can move on to check the organisation for the person
        }

        $results = $this->getRepo('OrganisationPerson')
            ->fetchByOrgAndPerson($application->getRelatedOrganisation(), $person);

        return !empty($results);
    }
}

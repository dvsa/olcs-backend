<?php

/**
 * Grant People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as OrganisationPersonRepository;
use Dvsa\Olcs\Api\Domain\Util\EntityCloner;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOrganisationPerson;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;

/**
 * Grant People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GrantPeople extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Person', 'OrganisationPerson'];

    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $applicationOrgPeople = $application->getApplicationOrganisationPersons();

        if ($applicationOrgPeople->count() < 1) {
            return $this->result;
        }

        /** @var ApplicationOrganisationPerson $applicationOrgPerson */
        foreach ($applicationOrgPeople as $applicationOrgPerson) {
            switch ($applicationOrgPerson->getAction()) {
                case 'A':
                    $this->createOrganisationPerson($applicationOrgPerson);
                    break;
                case 'U':
                    $this->updateOrganisationPerson($applicationOrgPerson);
                    break;
                case 'D':
                    $this->deleteOrganisationPerson($applicationOrgPerson);
                    break;
            }
        }

        $this->result->addMessage('Organisation person records have been copied');

        $personCount = $this->getOrganisationRepository()->fetchCountForOrganisation(
            $application->getLicence()->getOrganisation()->getId()
        );

        if ($personCount == 0) {
            $this->handleSideEffect(
                CreateTask::create(
                    [
                        'category' => Category::CATEGORY_APPLICATION,
                        'subCategory' => Category::TASK_SUB_CATEGORY_DIRECTOR_CHANGE_DIGITAL,
                        'description' => 'Last person removed',
                        'licence' => $application->getLicence()->getId(),
                    ]
                )
            );
            $this->result->addMessage('Task created as there are no people in the organisation');
        }

        return $this->result;
    }

    /**
     * Create a person and associate it with a new organisation person record
     *
     * @param ApplicationOrganisationPerson $aop
     */
    private function createOrganisationPerson(ApplicationOrganisationPerson $aop)
    {
        $sourcePerson = $aop->getPerson();
        $targetPerson = EntityCloner::cloneEntity($sourcePerson);

        $this->getRepo('Person')->save($targetPerson);

        $ignore = ['action', 'originalPerson', 'person'];

        /** @var OrganisationPerson $targetOp */
        $targetOp = EntityCloner::cloneEntityInto($aop, OrganisationPerson::class, $ignore);

        /** Application */
        $targetOp->setPerson($targetPerson);

        $this->getOrganisationRepository()->save($targetOp);
    }

    /**
     * Updates are actually just a combination of a delete
     * of the original person and an add of the new one
     *
     * @param ApplicationOrganisationPerson $aop
     */
    private function updateOrganisationPerson(ApplicationOrganisationPerson $aop)
    {
        $this->deleteByOrgAndPerson($aop->getOrganisation(), $aop->getOriginalPerson());

        $this->createOrganisationPerson($aop);
    }

    /**
     * Delete a person
     *
     * @param array $data
     */
    private function deleteOrganisationPerson(ApplicationOrganisationPerson $aop)
    {
        $this->deleteByOrgAndPerson($aop->getOrganisation(), $aop->getPerson());
    }

    /**
     * Helper to delete both an org row and the person it
     * links to
     *
     * @param Organisation $org
     * @param Person       $person
     */
    private function deleteByOrgAndPerson(Organisation $org, Person $person)
    {
        $orgPersonRecords = $this->getOrganisationRepository()->fetchByOrgAndPerson($org, $person);

        foreach ($orgPersonRecords as $orgPersonRecord) {
            $this->getOrganisationRepository()->delete($orgPersonRecord);
        }
    }

    /**
     * Get the OrganisationPerson repository
     *
     * @return OrganisationPersonRepository
     */
    private function getOrganisationRepository()
    {
        /** @var OrganisationPersonRepository $repository */
        $repository = $this->getRepo('OrganisationPerson');
        return $repository;
    }
}

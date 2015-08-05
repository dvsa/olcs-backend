<?php

/**
 * DeletePeople
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * DeletePeople
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class DeletePeople extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';
    protected $extraRepos = ['OrganisationPerson', 'ApplicationOrganisationPerson','Person'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Transfer\Command\Licence\DeletePeople */

        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($command);
        $result = new Result();

        foreach ($command->getPersonIds() as $personId) {
            if ($application->useDeltasInPeopleSection()) {
                $this->deltaDeletePerson($application, $personId, $result);
            } else {
                $this->deleteOrganisationPerson($application, $personId, $result);
            }
        }

        $dtoData = ['id' => $application->getId(), 'section' => 'people'];
        $result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::create($dtoData)
            )
        );

        return $result;
    }

    /**
     * Delete a (Application)OrganisationPerson by using deltas
     *
     * @param ApplicationEntity $application
     * @param type              $personId    ID of person to delete
     * @param Result            $result      Result object to add messages to
     */
    private function deltaDeletePerson(ApplicationEntity $application, $personId, Result $result)
    {
        try {
            $appOrgPerson = $this->getRepo('ApplicationOrganisationPerson')
                ->fetchForApplicationAndPerson($application->getId(), $personId);

            $result->addMessage("ApplicationOrganisationPerson ID {$appOrgPerson->getId()} deleted");
            $this->getRepo('ApplicationOrganisationPerson')->delete($appOrgPerson);
            $this->getRepo('Person')->delete($appOrgPerson->getPerson());
        } catch (\Dvsa\Olcs\Api\Domain\Exception\NotFoundException $e) {
            $appOrgPerson = new \Dvsa\Olcs\Api\Entity\Application\ApplicationOrganisationPerson(
                $application,
                $application->getLicence()->getOrganisation(),
                $this->getRepo()->getReference(\Dvsa\Olcs\Api\Entity\Person\Person::class, $personId)
            );
            $appOrgPerson->setAction('D');

            $application->addApplicationOrganisationPersons($appOrgPerson);
            $this->getRepo('ApplicationOrganisationPerson')->save($appOrgPerson);

            $result->addMessage("ApplicationOrganisationPerson ID {$appOrgPerson->getId()} delete delta created");
        }
    }

    /**
     * Delete an OrganisationPerson
     *
     * @param ApplicationEntity $application
     * @param type              $personId    ID of person to delete
     * @param Result            $result      Result object to add messages to
     */
    private function deleteOrganisationPerson(ApplicationEntity $application, $personId, Result $result)
    {
        $organisationPersons = $this->getRepo('OrganisationPerson')->fetchListForOrganisationAndPerson(
            $application->getLicence()->getOrganisation()->getId(),
            $personId
        );
        // There should only be one, but just in case iterate them
        foreach ($organisationPersons as $organisationPerson) {
            $this->getRepo('OrganisationPerson')->delete($organisationPerson);
            $result->addMessage("OrganisationPerson ID {$organisationPerson->getId()} deleted");
        }

        // if no other OrganisationPerson relates to the person ID
        if (count($this->getRepo('OrganisationPerson')->fetchListForPerson($personId)) === 0) {
            $this->getRepo('Person')->delete($organisationPerson->getPerson());
            $result->addMessage("Person ID {$organisationPerson->getPerson()->getId()} deleted");
        }
    }
}

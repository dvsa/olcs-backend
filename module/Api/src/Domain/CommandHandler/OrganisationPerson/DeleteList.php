<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete a list of OrganisationPerson
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class DeleteList extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'OrganisationPerson';

    protected $extraRepos = ['Person', 'ApplicationOrganisationPerson'];

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Transfer\Command\OrganisationPerson\DeleteList $command Command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $organisationPersonId) {
            /** @var \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson $organisationPerson */
            $organisationPerson = $this->getRepo()->fetchById($organisationPersonId);
            $this->getRepo()->delete($organisationPerson);
            $result->addMessage("OrganisationPerson ID {$organisationPersonId} deleted");

            $organisationPersons = $this->getRepo('OrganisationPerson')->fetchListForPerson(
                $organisationPerson->getPerson()->getId()
            );
            if (count($organisationPersons) === 0) {
                // if no organisation person records for this person, then person can be deleted
                $this->getRepo('Person')->delete($organisationPerson->getPerson());
                $result->addMessage("Person ID {$organisationPerson->getPerson()->getId()} deleted");

                // remove all application_organisation_person records for this person
                $this->getRepo('ApplicationOrganisationPerson')->deleteForPerson($organisationPerson->getPerson());
            }
        }

        return $result;
    }
}

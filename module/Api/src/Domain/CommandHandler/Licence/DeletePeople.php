<?php

/**
 * DeletePeople
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;

/**
 * DeletePeople
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class DeletePeople extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['OrganisationPerson', 'Person'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Transfer\Command\Licence\DeletePeople */

        /* @var $licence LicenceEntity */
        $licence = $this->getRepo()->fetchUsingId($command);

        $result = new Result();
        foreach ($command->getPersonIds() as $personId) {

            $organisationPersons = $this->getRepo('OrganisationPerson')->fetchListForOrganisationAndPerson(
                $licence->getOrganisation()->getId(),
                $personId
            );
            // There should only be one, but just in case iterate them
            foreach ($organisationPersons as $organisationPerson) {
                $this->getRepo('OrganisationPerson')->delete($organisationPerson);
                $result->addMessage("OrganisatonPerson ID {$organisationPerson->getId()} deleted");
            }

            // if no other OrganisationPerson relates to the person ID
            if (count($this->getRepo('OrganisationPerson')->fetchListForPerson($personId)) === 0) {
                $this->getRepo('Person')->delete($organisationPerson->getPerson());
                $result->addMessage("Person ID {$organisationPerson->getPerson()->getId()} deleted");
            }
        }

        return $result;
    }
}

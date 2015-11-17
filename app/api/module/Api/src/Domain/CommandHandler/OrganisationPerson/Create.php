<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an OrganisationPerson
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'OrganisationPerson';
    protected $extraRepos = ['Person'];

    public function handleCommand(CommandInterface $command)
    {
        $person = new Person();
        $person->updatePerson(
            $command->getPerson()['forename'],
            $command->getPerson()['familyName'],
            $this->getRepo()->getRefdataReference($command->getPerson()['title']),
            $command->getPerson()['birthDate']
        );
        $person->setOtherName($command->getPerson()['otherName']);
        $this->getRepo('Person')->save($person);

        $organisationPerson = new OrganisationPerson();
        $organisationPerson->setPosition($command->getPosition());
        $organisationPerson->setOrganisation(
            $this->getRepo()->getReference(
                \Dvsa\Olcs\Api\Entity\Organisation\Organisation::class,
                $command->getOrganisation()
            )
        );
        $organisationPerson->setPerson($person);
        $this->getRepo()->save($organisationPerson);

        $result = new Result();
        $result->addId('organisationPerson', $organisationPerson->getId());
        $result->addId('person', $person->getId());
        $result->addMessage("Organisation Person ID {$organisationPerson->getId()} created");

        return $result;
    }
}

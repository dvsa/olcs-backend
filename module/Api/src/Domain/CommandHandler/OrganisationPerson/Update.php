<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Update OrganisationPerson
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'OrganisationPerson';
    protected $extraRepos = ['Person'];

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $organisationPerson \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson */
        $organisationPerson = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $organisationPerson->setPosition($command->getPosition());
        $this->getRepo()->save($organisationPerson);

        $person = $organisationPerson->getPerson();
        $person->updatePerson(
            $command->getPerson()['forename'],
            $command->getPerson()['familyName'],
            $this->getRepo()->getRefdataReference($command->getPerson()['title']),
            $command->getPerson()['birthDate']
        );
        $person->setOtherName($command->getPerson()['otherName']);
        $this->getRepo('Person')->save($person);

        $result = new Result();
        $result->addMessage('OrganisationPerson updated');
        $result->addId('organisationPerson', $organisationPerson->getId());

        return $result;
    }
}

<?php

/**
 * CreatePeople
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
 * CreatePeople
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CreatePeople extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['OrganisationPerson', 'Person'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Transfer\Command\Licence\CreatePeople */

        /* @var $licence LicenceEntity */
        $licence = $this->getRepo()->fetchUsingId($command);

        if (!$licence->getOrganisation()->isSoleTrader() && !$licence->getOrganisation()->isPartnership() ) {
            throw new BadRequestException('Only sole traders and partnerships can be editied');
        }

        $person = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person->updatePerson(
            $command->getForename(),
            $command->getFamilyName(),
            $this->getRepo()->getRefdataReference($command->getTitle()),
            $command->getBirthDate()
        );
        $person->setOtherName($command->getOtherName());
        $this->getRepo('Person')->save($person);

        $organisationPerson = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson();
        $organisationPerson->setOrganisation($licence->getOrganisation());
        $organisationPerson->setPerson($person);
        $this->getRepo('OrganisationPerson')->save($organisationPerson);

        $result = new Result();
        $result->addMessage('OrganisatonPerson created');
        $result->addId('organisatonPerson', $organisationPerson->getId());
        $result->addId('person', $person->getId());
        return $result;
    }
}

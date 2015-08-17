<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Disqualification;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Disqualification;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Disqualification\Create as Command;

/**
 * Creates a Disqualification
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Disqualification';
    protected $extraRepos = ['Person', 'ContactDetails'];

    /**
     * Creates a Disqualification
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */

        $disqualification = new Disqualification($this->getOrganisation($command), $this->getOfficerCd($command));
        $disqualification->update(
            $command->getIsDisqualified(),
            $command->getStartDate() ? new \DateTime($command->getStartDate()) : null,
            $command->getNotes(),
            $command->getPeriod() ?: null
        );

        $this->getRepo()->save($disqualification);

        $result = new Result();

        $result->addId('disqualification', $disqualification->getId());
        $result->addMessage('Disqualification created');

        return $result;
    }

    /**
     * Get a refernece to the Organisation entity
     *
     * @param Command $command
     *
     * @return Organisation|null
     */
    private function getOrganisation(Command $command)
    {
        return $command->getOrganisation() ?
            $this->getRepo()->getReference(Organisation::class, $command->getOrganisation()) :
            null;
    }

    /**
     * Get a refernece to the Officer ContactDetails entity
     *
     * @param Command $command
     *
     * @return ContactDetails|null
     */
    private function getOfficerCd(Command $command)
    {
        if (empty($command->getPerson())) {
            return null;
        }

        /* @var $person \Dvsa\Olcs\Api\Entity\Person\Person */
        $person = $this->getRepo('Person')->fetchById($command->getPerson());

        // if contact details don't exists then create them
        if (!$person->getContactDetail()) {
            $contactDetails = new ContactDetails(
                $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_CORRESPONDENCE_ADDRESS)
            );
            $contactDetails->setPerson($person);
            $this->getRepo('ContactDetails')->save($contactDetails);
            $person->addContactDetails($contactDetails);
        }

        return $person->getContactDetail();
    }
}

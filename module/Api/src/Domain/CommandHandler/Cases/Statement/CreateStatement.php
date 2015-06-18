<?php

/**
 * Create Statement
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Statement;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Statement;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Transfer\Command\Cases\Statement\CreateStatement as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Create Statement
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class CreateStatement extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Statement';

    protected $extraRepos = ['ContactDetails', 'Cases'];
    /**
     * Creates opposition  and associated entities
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $contactDetails = $this->createContactDetailsObject($command);
        $result->addMessage('Contact details created');

        $statement = $this->createStatementObject($command);
        $result->addMessage('Statement created');

        $this->getRepo()->save($opposition);

        $result->addId('opposition ', $opposition->getId());
        $result->addId('opposer ', $opposer->getId());
        $result->addId('contactDetails', $contactDetails->getId());

        return $result;
    }

    private function createContactDetailsObject($command)
    {
        return ContactDetails::create(
            $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_STATEMENT_REQUESTOR),
            $this->getRepo('ContactDetails')->populateRefDataReference(
                $command->getRequestorsContactDetails()
            )
        );
    }

    /**
     * Create the opposition object
     *
     * @param Cmd $command
     * @param Opposer $opposer
     * @return Statement
     */
    private function createStatementObject(Cmd $command, ContactDetails $contactDetails)
    {
        $case = $this->getRepo()->getReference(Cases::class, $command->getCase());

        $statement = new Statement(
            $case,
            $this->getRepo()->getRefdataReference($command->getStatementType()),
        );

        if (!is_null($application)) {
            $opposition->setApplication($application);
        }

        if ($command->getRaisedDate() !== null) {
            $opposition->setRaisedDate(new \DateTime($command->getRaisedDate()));
        }

        if ($command->getIsValid() !== null) {
            $opposition->setIsValid($this->getRepo()->getRefdataReference($command->getIsValid()));
        }

        if ($command->getValidNotes() !== null) {
            $opposition->setValidNotes($command->getValidNotes());
        }

        if ($command->getStatus() !== null) {
            $opposition->setStatus($this->getRepo()->getRefdataReference($command->getStatus()));
        }

        $operatingCentres = $this->generateOperatingCentres($command);
        $opposition->setOperatingCentres($operatingCentres);

        if ($command->getGrounds() !== null) {
            $opposition->setGrounds($this->getRepo()->generateRefdataArrayCollection($command->getGrounds()));
        }

        if ($command->getNotes() !== null) {
            $opposition->setNotes($command->getNotes());
        }

        return $opposition;
    }

    /**
     * Create the opposer  object
     *
     * @param Cmd $command
     * @param ContactDetails $contactDetails
     * @return Opposer
     */
    private function createOpposerObject(Cmd $command, ContactDetails $contactDetails)
    {
        $opposer = new Opposer(
            $contactDetails,
            $this->getRepo()->getRefdataReference($command->getOpposerType()),
            $this->getRepo()->getRefdataReference($command->getStatementType())
        );

        return $opposer;
    }

    /**
     * Generate list of operatingCentres based on type of opposition. At present it allows both types to specify OCs
     * This may need to be either one or the other.
     *
     * @param Cmd $command
     * @return ArrayCollection
     */
    private function generateOperatingCentres(Cmd $command)
    {
        $collection = new ArrayCollection();
        if (!empty($command->getLicenceOperatingCentres() || !empty($command->getApplicationOperatingCentres()))) {

            if (!empty($command->getLicenceOperatingCentres())) {
                $operatingCentres = $command->getLicenceOperatingCentres();
                foreach ($operatingCentres as $oc) {
                    $collection->add($this->getRepo()->getReference(OperatingCentre::class, $oc));
                }
            }

            if (!empty($command->getApplicationOperatingCentres())) {
                $operatingCentres = $command->getApplicationOperatingCentres();
                foreach ($operatingCentres as $oc) {
                    $collection->add($this->getRepo()->getReference(OperatingCentre::class, $oc));
                }
            }
        }

        return $collection;
    }
}

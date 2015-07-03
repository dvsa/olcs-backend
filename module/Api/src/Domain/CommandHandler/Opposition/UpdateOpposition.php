<?php

/**
 * Update Opposition
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Opposition;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Opposition\Opposition;
use Dvsa\Olcs\Api\Entity\Opposition\Opposer;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Transfer\Command\Opposition\UpdateOpposition as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Update Opposition
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class UpdateOpposition extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Opposition';

    protected $extraRepos = ['ContactDetails', 'Cases'];
    /**
     * Updates opposition  and associated entities
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var $opposition Opposition */
        $opposition = $this->updateOppositionObject($command);
        $result->addMessage('Opposition updated');

        $opposition->getOpposer()->update(
            [
                'opposerType' => $this->getRepo()->getRefdataReference($command->getOpposerType()),
                'oppositionType' => $this->getRepo()->getRefdataReference($command->getOppositionType()),
            ]
        );
        $result->addMessage('Opposer updated');

        $opposition->getOpposer()->getContactDetails()->update(
            $this->getRepo('ContactDetails')->populateRefDataReference(
                $command->getOpposerContactDetails()
            )
        );

        $result->addMessage('Contact details updated');

        $this->getRepo()->save($opposition);

        $result->addId('opposition ', $opposition->getId());
        $result->addId('opposer', $opposition->getOpposer()->getId());
        $result->addId('contactDetails', $opposition->getOpposer()->getContactDetails()->getId());

        return $result;
    }

    /**
     * Update the opposition  object
     *
     * @param Cmd $command
     * @return Opposition
     */
    private function updateOppositionObject(Cmd $command)
    {
        $opposition = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

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

<?php

/**
 * Update Opposition
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Opposition;

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
use Dvsa\Olcs\Transfer\Command\Cases\Opposition\UpdateOpposition as Cmd;
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

        $opposition = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $contactDetails = $this->updateContactDetailsObject($command, $opposition->getOpposer()->getContactDetails());
        $result->addMessage('Contact details updated');

        $opposition->getOpposer()->setContactDetails($contactDetails);
        $result->addMessage('Opposer updated');

        $opposition = $this->updateOppositionObject($command, $opposition);
        $result->addMessage('Opposition updated');

        $this->getRepo()->save($opposition);

        $result->addId('opposition ', $opposition->getId());
        $result->addId('opposer ', $opposer->getId());
        $result->addId('contactDetails', $contactDetails->getId());

        return $result;
    }

    private function updateContactDetailsObject($command, ContactDetails $contactDetails)
    {
        return $contactDetails->update(
            $this->getRepo('ContactDetails')->populateRefDataReference(
                $this->getObjectorContactDetails($command)
            )
        );
    }

    private function getObjectorContactDetails(Cmd $command)
    {
        $objectorContactDetails['emailAddress'] = $command->getEmailAddress();

        $objectorContactDetails['address'] = $this->updateAddressArray($command);

        $objectorContactDetails['person'] = $this->updatePersonArray($command);

        $objectorContactDetails['phoneContacts'] = $this->updatePhoneContactsArray($command);

        return $objectorContactDetails;
    }

    /**
     * Update the opposition  object
     *
     * @param Cmd $command
     * @param Opposer $opposer
     * @return Opposition
     */
    private function updateOppositionObject(Cmd $command, Opposition $opposition)
    {
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
     * Update the opposer  object
     *
     * @param Cmd $command
     * @param ContactDetails $contactDetails
     * @return Opposer
     */
    private function updateOpposerObject(Cmd $command, ContactDetails $contactDetails)
    {
        $opposer = new Opposer(
            $contactDetails,
            $this->getRepo()->getRefdataReference($command->getOpposerType()),
            $this->getRepo()->getRefdataReference($command->getOppositionType())
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

    /**
     * Update person array
     * @param Cmd $command
     * @return Person|null
     */
    private function updatePersonArray(Cmd $command)
    {
        if (!empty($command->getForename()) || !empty($command->getForename())) {
            $person = [];
            $person['forename'] = $command->getForename();
            $person['familyName'] = $command->getFamilyName();
            return $person;
        }
        return null;
    }

    /**
     * Update address array
     * @param Cmd $command
     * @return Address|null
     */
    private function updateAddressArray(Cmd $command)
    {
        if (!empty($command->getOpposerAddress()['addressLine1']) ||
            !empty($command->getOpposerAddress()['addressLine2']) ||
            !empty($command->getOpposerAddress()['addressLine3']) ||
            !empty($command->getOpposerAddress()['addressLine4']) ||
            !empty($command->getOpposerAddress()['town']) ||
            !empty($command->getOpposerAddress()['postcode']) ||
            !empty($command->getOpposerAddress()['countryCode'])
        ) {
            return $command->getOpposerAddress();
        }
        return null;
    }

    /**
     * Generates an array of phone contact details
     *
     * @param Cmd $command
     * @return ArrayCollection|null
     */
    private function updatePhoneContactsArray(Cmd $command)
    {
        $phoneContacts = [];
        if (!is_null($command->getPhone()))
        {
            $phoneContact = [];
            $phoneContact['phoneContactType'] = PhoneContact::PHONE_CONTACT_TYPE_TEL;
            $phoneContact['phoneNumber'] = $command->getPhone();

            $phoneContacts[] = $phoneContact;
            return $phoneContacts;
        }

        return null;
    }
}

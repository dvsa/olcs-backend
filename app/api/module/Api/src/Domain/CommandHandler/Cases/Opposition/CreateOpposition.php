<?php

/**
 * Create Opposition
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Opposition;

use Common\Service\Entity\ContactDetailsEntityService;
use Doctrine\Common\Collections\ArrayCollection;
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
use Dvsa\Olcs\Transfer\Command\Cases\Opposition\CreateOpposition as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Create Opposition
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class CreateOpposition  extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Opposition ';

    /**
     * Creates opposition  and associated entities
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $address = $this->createAddressObject($command);
        $result->addMessage('Address created');

        $person = $this->createPersonObject($command);
        $result->addMessage('Person created');

        $phoneContacts = $this->createPhoneContactsObject($command);
        $result->addMessage('Phone contacts created');

        $contactDetails = ContactDetails::opposer($address, $person, $phoneContacts, $command->getEmailAddress());
        $result->addMessage('Contact details created');

        $opposer = $this->createOpposerObject($command, $contactDetails);
        $result->addMessage('Opposer created');

        $opposition = $this->createOppositionObject($command, $opposer);
        $result->addMessage('Opposition created');


        $this->getRepo()->save($opposition);
        $result->addMessage('Opposition  created');

        $result->addId('opposition ', $opposition ->getId());
        $result->addId('opposer ', $opposer ->getId());
        $result->addId('person', $person->getId());
        $result->addId('contactDetails', $contactDetails->getId());

        return $result;
    }

    /**
     * Get Licence for case
     * @param Cmd $command
     * @return Licence
     */
    private function getLicenceObject(Cmd $command)
    {
        $case = $this->getRepo('Cases')->fetchUsingId($command->getCase(), Query::HYDRATE_OBJECT);
        return $case->getLicence();
    }

    /**
     * Create the opposition  object
     *
     * @param Cmd $command
     * @param Opposer $opposer
     * @return Opposition
     */
    private function createOppositionObject(Cmd $command, Opposer $opposer)
    {
        $isPublicInquiry = 'N';

        $licence = $this->getLicenceObject($command);

        $opposition = new Opposition(
            $licence,
            $opposer,
            $this->getRepo()->getReference(Cases::class, $command->getOppositionType()),
            $this->getRepo()->getReference($command->getIsCopied()),
            $this->getRepo()->getReference($command->getIsInTime()),
            $isPublicInquiry,
            $this->getRepo()->getReference($command->getIsWillingToAttendPi()),
            $this->getRepo()->getReference($command->getIsWithdrawn())
    );

        if ($command->getRaisedDate() !== null) {
            $opposition->setRaisedDate(new \DateTime($command->getRaisedDate()));
        }

        if ($command->getIsValid() !== null) {
            $opposition->setIsValid($this->getRepo()->getReference($command->getIsValid()));
        }

        if ($command->getValidNotes() !== null) {
            $opposition->setValidNotes($command->getValidNotes());
        }

        if ($command->getStatus() !== null) {
            $opposition->setStatus($this->getRepo()->getReference($command->getStatus()));
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
     * @param Opposer $opposer
     * @return Opposer
     */
    private function createOpposerObject(Cmd $command, ContactDetails $contactDetails)
    {
        $opposer = new Opposer(
            $contactDetails,
            $this->getRepo()->getReference(Cases::class, $command->getOpposerType()),
            $this->getRepo()->getReference(Cases::class, $command->getOppositionType())
        );

        return $opposer;
    }

    /**
     * Generate list of operatingCentres based on type of opposition
     *
     * @param Cmd $command
     * @return ArrayCollection
     */
    private function generateOperatingCentres(Cmd $command)
    {
        if (!empty($command->getLicenceOperatingCentres())) {
            return $this->getRepo()->generateRefdataArrayCollection($command->getLicenceOperatingCentres());
        }
        if (!empty($command->getApplicationOperatingCentres())) {
            return $this->getRepo()->generateRefdataArrayCollection($command->getApplicationOperatingCentres());
        }
        return null;
    }

    /**
     * Create person object
     * @param Cmd $command
     * @return Person|null
     */
    private function createPersonObject(Cmd $command)
    {
        if (!empty($command->getForename()) || !empty($command->getForename())) {
            $person = new Person();
            $person->setForename($command->getForename());
            $person->setFamilyName($command->getFamilyName());
            return $person;
        }
        return null;
    }

    /**
     * Create address object
     * @param Cmd $command
     * @return Address|null
     */
    private function createAddressObject(Cmd $command)
    {
        if (!empty($command->getOpposerAddress()['addressLine1']) ||
            !empty($command->getOpposerAddress()['addressLine2']) ||
            !empty($command->getOpposerAddress()['addressLine3']) ||
            !empty($command->getOpposerAddress()['addressLine4']) ||
            !empty($command->getOpposerAddress()['town']) ||
            !empty($command->getOpposerAddress()['postcode']) ||
            !empty($command->getOpposerAddress()['countryCode'])
        ) {
            $address = new Address();
            $address->setAddressLine1($command->getAddressLine1());
            $address->setAddressLine2($command->getAddressLine2());
            $address->setAddressLine3($command->getAddressLine3());
            $address->setAddressLine4($command->getAddressLine4());
            $address->setTown($command->getTown());
            $address->setPostcode($command->getPostcode());
            $address->setCountryCode($command->getCountryCode());

            return $address;
        }
        return null;
    }

    /**
     * Generates an ArrayCollection of PhoneContact entities
     *
     * @param Cmd $command
     * @return ArrayCollection|null
     */
    private function createPhoneContactsObjects(Cmd $command)
    {
        $phoneContacts = new ArrayCollection();
        if (!is_null($command->getPhone()))
        {
            $phoneContact = new PhoneContact(PhoneContact::PHONE_CONTACT_TYPE_TEL);
            $phoneContact->setPhoneNumber($command->getPhone());

            $phoneContacts->add($phoneContact);
        }
        if (!is_null($command->getF))
        {
            $phoneContact = new PhoneContact(PhoneContact::PHONE_CONTACT_TYPE_TEL);
            $phoneContact->setPhoneNumber($command->getPhone());

            $phoneContacts->add($phoneContact);
        }
        return $phoneContacts;
    }
}

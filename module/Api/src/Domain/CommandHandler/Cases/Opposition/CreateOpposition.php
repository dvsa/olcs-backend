<?php

/**
 * Create Opposition
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
use Dvsa\Olcs\Transfer\Command\Cases\Opposition\CreateOpposition as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Create Opposition
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class CreateOpposition extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Opposition';

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

        $opposer = $this->createOpposerObject($command, $contactDetails);
        $result->addMessage('Opposer created');

        $opposition = $this->createOppositionObject($command, $opposer);
        $result->addMessage('Opposition created');

        $this->getRepo()->save($opposition);

        $result->addId('opposition ', $opposition->getId());
        $result->addId('opposer ', $opposer->getId());
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
        $case = $this->getRepo('Cases')->fetchById($command->getCase(), Query::HYDRATE_OBJECT);
        return $case->getLicence();
    }

    private function createContactDetailsObject($command)
    {
        return ContactDetails::create(
            $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_OBJECTOR),
            $this->getRepo('ContactDetails')->populateRefDataReference(
                $this->getObjectorContactDetails($command)
            )
        );
    }

    private function getObjectorContactDetails(Cmd $command)
    {
        $objectorContactDetails['emailAddress'] = $command->getEmailAddress();

        $objectorContactDetails['address'] = $this->createAddressArray($command);

        $objectorContactDetails['person'] = $this->createPersonArray($command);

        $objectorContactDetails['phoneContacts'] = $this->createPhoneContactsArray($command);

        return $objectorContactDetails;
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
        $case = $this->getRepo()->getReference(Cases::class, $command->getCase());
        $application = $case->getApplication();

        $opposition = new Opposition(
            $case,
            $licence,
            $opposer,
            $this->getRepo()->getRefdataReference($command->getOppositionType()),
            $this->getRepo()->getRefdataReference($command->getIsValid()),
            $command->getIsCopied(),
            $command->getIsInTime(),
            $isPublicInquiry,
            $command->getIsWillingToAttendPi(),
            $command->getIsWithdrawn()
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
     * Create person array
     * @param Cmd $command
     * @return Person|null
     */
    private function createPersonArray(Cmd $command)
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
     * Create address array
     * @param Cmd $command
     * @return Address|null
     */
    private function createAddressArray(Cmd $command)
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
    private function createPhoneContactsArray(Cmd $command)
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

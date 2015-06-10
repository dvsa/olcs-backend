<?php

/**
 * Update IrfoDetails
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Organisation\UpdateTradingNames;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPartner;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Partial\Address as AddressCmd;
use Doctrine\ORM\Query;

/**
 * Update IrfoDetails
 */
final class UpdateIrfoDetails extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Organisation';

    protected $extraRepos = ['IrfoPartner', 'PhoneContact'];

    public function handleCommand(CommandInterface $command)
    {
        $org = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        if ($command->getIrfoNationality() !== null) {
            $org->setIrfoNationality(
                $this->getRepo()->getReference(Country::class, $command->getIrfoNationality())
            );
        }

        if ($command->getIrfoContactDetails() !== null) {

            if ($org->getIrfoContactDetails() instanceof ContactDetails) {
                // update existing contact details

                $address = ($command->getIrfoContactDetails()['address'] !== null) ?
                    $this->populateAddress(
                        AddressCmd::create($command->getIrfoContactDetails()['address']),
                        $org->getIrfoContactDetails()->getAddress()
                    ) :
                    null;

                $phoneContacts = ($command->getIrfoContactDetails()['phoneContacts'] !== null) ?
                    $this->populatePhoneContacts(
                        $command->getIrfoContactDetails()['phoneContacts'],
                        $org->getIrfoContactDetails()
                    ) :
                    null;

                $org->getIrfoContactDetails()->updateForIrfoOperator(
                    $address,
                    $phoneContacts,
                    $command->getIrfoContactDetails()['emailAddress']
                );
            } else {
                // create new contact details

                $address = ($command->getIrfoContactDetails()['address'] !== null) ?
                    $this->populateAddress(
                        AddressCmd::create($command->getIrfoContactDetails()['address'])
                    ) :
                    null;

                $phoneContacts = ($command->getIrfoContactDetails()['phoneContacts'] !== null) ?
                    $this->populatePhoneContacts(
                        $command->getIrfoContactDetails()['phoneContacts']
                    ) :
                    null;

                $org->setIrfoContactDetails(
                    ContactDetails::createForIrfoOperator(
                        $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_IRFO_OPERATOR),
                        $address,
                        $phoneContacts,
                        $command->getIrfoContactDetails()['emailAddress']
                    )
                );
            }
        }

        $this->getRepo()->save($org);

        if ($command->getTradingNames() !== null) {
            // deal with TradingNames
            $this->processTradingNames($org, $command->getTradingNames());
        }

        if ($command->getIrfoPartners() !== null) {
            // deal with IrfoPartners
            $this->processIrfoPartners($org, $command->getIrfoPartners());
        }

        $result = new Result();
        $result->addId('organisation', $org->getId());
        $result->addMessage('IRFO Details updated successfully');

        return $result;
    }

    /**
     * @param array $phoneContacts Array of Dvsa\Olcs\Transfer\Command\Partial\PhoneContact
     * @param ContactDetails $contactDetails
     * @return array
     */
    private function populatePhoneContacts(array $phoneContacts, ContactDetails $contactDetails = null)
    {
        $reduced = $updatedIds = [];

        foreach ($phoneContacts as $phoneContact) {
            if (empty($phoneContact['phoneNumber'])) {
                // filter out empty values
                continue;
            }

            if (!empty($phoneContact['id'])) {
                // update
                $phoneContactEntity = $this->getRepo('PhoneContact')->fetchById(
                    $phoneContact['id'],
                    Query::HYDRATE_OBJECT,
                    $phoneContact['version']
                );
                $updatedIds[] = $phoneContactEntity->getId();
            } else {
                // create
                $phoneContactEntity = new PhoneContact(
                    $this->getRepo()->getRefdataReference($phoneContact['phoneContactType'])
                );

                if ($contactDetails !== null) {
                    $phoneContactEntity->setContactDetails($contactDetails);
                }
            }

            $phoneContactEntity->setPhoneNumber($phoneContact['phoneNumber']);

            $reduced[] = $phoneContactEntity;
        }

        if ($contactDetails !== null) {
            // remove the rest
            foreach ($contactDetails->getPhoneContacts() as $phoneContactEntity) {
                if (!in_array($phoneContactEntity->getId(), $updatedIds)) {
                    $this->getRepo('PhoneContact')->delete($phoneContactEntity);
                }
            }
        }

        return $reduced;
    }

    /**
     * @param AddressCmd $command
     * @param Address $address
     * @return Address
     */
    private function populateAddress(AddressCmd $command, Address $address = null)
    {
        if (!($address instanceof Address)) {
            $address = new Address();
        }

        $address->updateAddress(
            $command->getAddressLine1(),
            $command->getAddressLine2(),
            $command->getAddressLine3(),
            $command->getAddressLine4(),
            $command->getTown(),
            $command->getPostcode(),
            $this->getRepo()->getReference(
                Country::class, $command->getCountryCode()
            )
        );

        return $address;
    }

    /**
     * @param Organisation $org
     * @param array $tradingNames
     * @return array
     */
    private function processTradingNames(Organisation $org, array $tradingNames)
    {
        return $this->getCommandHandler()->handleCommand(
            UpdateTradingNames::create(
                [
                    'organisation' => $org->getId(),
                    'tradingNames' => array_column($tradingNames, 'name')
                ]
            )
        );
    }

    /**
     * @param Organisation $org
     * @param array $irfoPartners
     * @return array
     */
    private function processIrfoPartners(Organisation $org, array $irfoPartners)
    {
        $reduced = [];

        foreach ($irfoPartners as $irfoPartner) {
            if (empty($irfoPartner['name'])) {
                // filter out empty values
                continue;
            }

            if (!empty($irfoPartner['id'])) {
                // update
                $irfoPartnerEntity = $this->getRepo('IrfoPartner')->fetchById(
                    $irfoPartner['id'],
                    Query::HYDRATE_OBJECT,
                    $irfoPartner['version']
                );
                $irfoPartnerEntity->setName($irfoPartner['name']);
            } else {
                // create
                $irfoPartnerEntity = new IrfoPartner(
                    $org,
                    $irfoPartner['name']
                );
            }

            $this->getRepo('IrfoPartner')->save($irfoPartnerEntity);
            $reduced[] = $irfoPartnerEntity->getId();
        }

        // remove the rest
        foreach ($org->getIrfoPartners() as $irfoPartnerEntity) {
            if (!in_array($irfoPartnerEntity->getId(), $reduced)) {
                $this->getRepo('IrfoPartner')->delete($irfoPartnerEntity);
            }
        }

        return $reduced;
    }
}

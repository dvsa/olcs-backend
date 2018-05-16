<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use \Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStoreWithMultipleAddresses as Cmd;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use \Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

final class GenerateAndStoreWithMultipleAddresses extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Document';

    protected $extraRepos = ['Licence'];

    protected $defaultSendToAddresses = [
        'correspondenceAddress'         => true,
        'establishmentAddress'          => true,
        'transportConsultantAddress'    => true,
        'registeredAddress'             => true,
        'operatingCentresAddresses'     => true,
    ];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $data = $command->getGenerateCommandData();
        $description = $data['description'];
        $addressBookmark = (string) $command->getAddressBookmark();
        $bookmarkBundle = $command->getBookmarkBundle();
        /** @var Licence $licenceRepo */
        $licenceRepo = $this->getRepo('Licence');
        $licence = $licenceRepo->fetchWithAddressesUsingId($data['licence']);

        $addresses = $this->retrieveAddresses($command, $licence);

        foreach ($addresses as $addressName => $addressValue) {
            $data['description'] = "$description ($addressName)";
            $data['knownValues'] = [$addressBookmark => $this->createAddressKnownValues($bookmarkBundle, $addressValue)];
            $result = $this->handleSideEffect(GenerateAndStore::create($data));
            $documentId = $result->getId('document');
            $this->result->addId('documents', $documentId, true);
        }
        return $this->result;
    }

    /**
     * @param Cmd $command
     * @param LicenceEntity $licence
     * @return array
     */
    private function retrieveAddresses(CommandInterface $command, $licence)
    {
        $sendToAddresses = array_merge($this->defaultSendToAddresses, $command->getSendToAddresses());

        $addresses = [];

        if ($sendToAddresses['correspondenceAddress']) {
            $addresses['correspondenceAddress'] = $licence->getCorrespondenceCd()->getAddress()->serialize();
        }

        if ($sendToAddresses['establishmentAddress']) {
            $addresses['establishmentAddress'] = $licence->getEstablishmentCd()->getAddress()->serialize();
        }

        if ($sendToAddresses['transportConsultantAddress']) {
            $addresses['transportConsultantAddress'] = $licence->getTransportConsultantCd()->getAddress()->serialize();
        }

        if ($sendToAddresses['registeredAddress']) {
            $addresses['registeredAddress'] = $licence->getOrganisation()->getContactDetails()->getAddress()->serialize();
        }

        if ($sendToAddresses['operatingCentresAddresses']) {
            $n = 0;
            /** @var LicenceOperatingCentre $licenceOperatingCentre */
            foreach ($licence->getOperatingCentres() as $licenceOperatingCentre) {
                $n++;
                $addresses['operatingCentreAddress' . $n] = $licenceOperatingCentre->getOperatingCentre()->getAddress()->serialize();
            }
        }

        $addresses = $this->validateAddresses($addresses);

        return $addresses;
    }

    /**
     * @param $values
     * @return bool
     */
    private function isEmptyAddress($values)
    {
        return $values['addressLine1'] === null || $values['addressLine1'] === "";
    }


    /**
     * @param array $addresses
     * @return array
     */
    private function validateAddresses($addresses)
    {
        $validatedAddresses = [];
        foreach ($addresses as $key => $values) {
            if ($this->isEmptyAddress($values)) {
                continue;
            }

            foreach ($validatedAddresses as $validatedAddressKey => $validatedAddressValues) {
                if (
                    $validatedAddressValues['addressLine1'] === $values['addressLine1'] &&
                    $validatedAddressValues['addressLine2'] === $values['addressLine2'] &&
                    $validatedAddressValues['addressLine3'] === $values['addressLine3'] &&
                    $validatedAddressValues['addressLine4'] === $values['addressLine4'] &&
                    $validatedAddressValues['town']         === $values['town'] &&
                    $validatedAddressValues['postcode']     === $values['postcode'] &&
                    $validatedAddressValues['countryCode']  === $values['countryCode']
                ) {
                    continue 2;
                }
            }

            $validatedAddresses[$key] = $values;
        }

        return $validatedAddresses;
    }


    /**
     * @param array $bookmarkBundle
     * @param array $address
     * @return array
     */
    private function createAddressKnownValues($bookmarkBundle, $address)
    {
        $knownValues = [];
        foreach ($bookmarkBundle as $bundleKey => $bundleValues) {
            foreach ($bundleValues as $bundleValue) {
                $knownValues[$bundleKey] = [$bundleValue => $address];
            }
        }

        return $knownValues;
    }

}

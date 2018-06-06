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
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
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
            $data = $this->addAddressToKnownValues($bookmarkBundle, $addressValue, $data, $addressBookmark);
            $data = $this->addAddressToMetadata($data, $addressName);
            $result = $this->handleSideEffect(GenerateAndStore::create($data));
            $documentId = $result->getId('document');
            $this->result->addId('documents', $documentId, true);
        }
        return $this->result;
    }

    /**
     * @param Cmd           $command
     * @param LicenceEntity $licence
     * @return array
     */
    private function retrieveAddresses(CommandInterface $command, $licence)
    {
        $sendToAddresses = array_merge($this->defaultSendToAddresses, $command->getSendToAddresses());

        $addresses = [];

        if ($sendToAddresses['correspondenceAddress'] &&
            $licence->getCorrespondenceCd() !== null &&
            $licence->getCorrespondenceCd()->getAddress() !== null) {
            $addresses['correspondenceAddress'] = $licence->getCorrespondenceCd()->getAddress()->serialize();
        }

        if ($sendToAddresses['establishmentAddress'] &&
            $licence->getEstablishmentCd() !== null &&
            $licence->getEstablishmentCd()->getAddress() !== null) {
            $addresses['establishmentAddress'] = $licence->getEstablishmentCd()->getAddress()->serialize();
        }

        if ($sendToAddresses['transportConsultantAddress'] &&
            $licence->getTransportConsultantCd() !== null &&
            $licence->getTransportConsultantCd()->getAddress() !== null) {
            $addresses['transportConsultantAddress'] = $licence->getTransportConsultantCd()->getAddress()->serialize();
        }

        if ($sendToAddresses['registeredAddress'] &&
            $licence->getOrganisation()->getContactDetails() !== null &&
            $licence->getOrganisation()->getContactDetails()->getAddress() !== null) {
            $addresses['registeredAddress'] = $licence
                ->getOrganisation()
                ->getContactDetails()
                ->getAddress()
                ->serialize();
        }

        if ($sendToAddresses['operatingCentresAddresses']) {
            $n = 0;
            /** @var LicenceOperatingCentre $licenceOperatingCentre */
            foreach ($licence->getOperatingCentres() as $licenceOperatingCentre) {
                if ($licenceOperatingCentre->getOperatingCentre() !== null &&
                    $licenceOperatingCentre->getOperatingCentre()->getAddress() !== null) {
                    $n++;
                    $addresses['operatingCentreAddress' . $n] = $licenceOperatingCentre
                        ->getOperatingCentre()
                        ->getAddress()
                        ->serialize();
                }
            }
        }

        $addresses = $this->validateAddresses($addresses);

        return $addresses;
    }

    private function isEmptyAddress($values)
    {
        return ($values['addressLine1'] === null || $values['addressLine1'] === "") &&
        ($values['addressLine2'] === null || $values['addressLine2'] === "") &&
        ($values['addressLine3'] === null || $values['addressLine3'] === "") &&
        ($values['addressLine4'] === null || $values['addressLine4'] === "");
    }

    private function validateAddresses($addresses)
    {
        $validatedAddresses = [];
        foreach ($addresses as $key => $values) {
            if ($this->isEmptyAddress($values)) {
                continue;
            }

            foreach ($validatedAddresses as $validatedAddressKey => $validatedAddressValues) {
                if ($validatedAddressValues['addressLine1'] === $values['addressLine1'] &&
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

    private function addAddressToMetadata($data, $addressName)
    {
        if (!array_key_exists('metadata', $data)) {
            $data['metadata'] = json_encode([]);
        }
        $metadata = json_decode($data['metadata'], true);
        if (!array_key_exists('details', $metadata)) {
            $metadata['details'] = [];
        }
        $metadata['details']['sendToAddress'] = $addressName;
        $data['metadata'] = json_encode($metadata);
        return $data;
    }

    private function addAddressToKnownValues($bookmarkBundle, $addressValue, $data, $addressBookmark)
    {
        if (!array_key_exists('knownValues', $data)) {
            $data['knownValues'] = [];
        }
        $knownValues = [];
        foreach ($bookmarkBundle as $bundleKey => $bundleValues) {
            foreach ($bundleValues as $bundleValue) {
                $knownValues[$bundleKey] = [$bundleValue => $addressValue];
            }
        }
        $data['knownValues'][$addressBookmark] = $knownValues;
        return $data;
    }
}

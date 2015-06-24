<?php

/**
 * Create Operator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Operator;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as OrganisationPersonEntity;
use Dvsa\Olcs\Transfer\Command\Operator\Create as Cmd;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress as SaveAddressCmd;

/**
 * Create Operator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateOperator extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Organisation';

    protected $extraRepos = ['OrganisationPerson', 'Person'];

    const ERROR_UNKNOWN_TYPE = 'ERR_ORG_1';

    public function handleCommand(CommandInterface $command)
    {
        $this->validateOrganisation($command);
        $type = $command->getBusinessType();

        $organisation = $this->createOrganisationObject($command);

        if ($type === OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY || $type === OrganisationEntity::ORG_TYPE_LLP) {
            $this->saveAddress($command, $organisation);
        }

        $this->getRepo()->save($organisation);

        if ($type === OrganisationEntity::ORG_TYPE_SOLE_TRADER) {
            $this->saveOrganisationPerson($command, $organisation);
        }

        $result = new Result();
        $result->addId('organisation', $organisation->getId());
        $result->addMessage('Organisation created successfully');

        return $result;
    }

    private function saveAddress(Cmd $command, $organisation)
    {
        $address = $command->getAddress();
        $address['contactType'] = AddressEntity::CONTACT_TYPE_REGISTERED_ADDRESS;
        $result = $this->getCommandHandler()->handleCommand(
            SaveAddressCmd::create($address)
        );
        $contactDetailsId = $result->getId('contactDetails');
        if ($contactDetailsId !== null) {
            $organisation->setContactDetails(
                $this->getRepo()->getReference(ContactDetailsEntity::class, $contactDetailsId)
            );
        }
    }

    private function saveOrganisationPerson(Cmd $command, $organisation)
    {
        $person = $this->createPersonObject($command);
        $this->getRepo('Person')->save($person);
        $organisationPerson = new OrganisationPersonEntity();
        $organisationPerson->setPerson($person);
        $organisationPerson->setOrganisation($organisation);
        $this->getRepo('OrganisationPerson')->save($organisationPerson);
    }

    /**
     * Create person object
     * @param Cmd $command
     * @return Person
     */
    private function createPersonObject(Cmd $command)
    {
        $person = new PersonEntity();

        $person->setForename($command->getFirstName());
        $person->setFamilyName($command->getLastName());

        return $person;
    }

    /**
     * @param Cmd $command
     * @return Organisation
     */
    private function createOrganisationObject(Cmd $command)
    {
        $organisation = new OrganisationEntity();

        $organisation->updateOrganisation(
            $command->getName(),
            $command->getCompanyNumber(),
            $command->getFirstName(),
            $command->getLastName(),
            ($command->getBusinessType() === OrganisationEntity::ORG_TYPE_IRFO) ? true : false
        );
        $organisation->setType($this->getRepo()->getRefdataReference($command->getBusinessType()));
        if ($command->getNatureOfBusiness() !== null) {
            $natureOfBusinesses = $command->getNatureOfBusiness();
            $references = [];
            foreach ($natureOfBusinesses as $natureOfBusiness) {
                $references[] = $this->getRepo()->getRefdataReference($natureOfBusiness);
            }
            $organisation->setNatureOfBusinesses($references);
        }

        return $organisation;
    }

    protected function validateOrganisation($command)
    {
        $type = $command->getBusinessType();
        switch ($type) {
            case OrganisationEntity::ORG_TYPE_PARTNERSHIP:
            case OrganisationEntity::ORG_TYPE_OTHER:
                $this->validatePartnershipOrOther($command);
                break;
            case OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY:
            case OrganisationEntity::ORG_TYPE_LLP:
                $this->validateRegOrLlp($command);
                break;
            case OrganisationEntity::ORG_TYPE_SOLE_TRADER:
                $this->validateSoleTrader($command);
                break;
            case OrganisationEntity::ORG_TYPE_IRFO:
                $this->validateIrfo($command);
                break;
            default:
                throw new ValidationException(
                    [self::ERROR_UNKNOWN_TYPE => 'Unknown business type']
                );
        }
    }

    protected function validatePartnershipOrOther($command)
    {
        $errors = [];
        if (!$command->getName()) {
            $errors[] = [
                'name' => [
                    'Operator name is required'
                ]
            ];
        }
        if (!$command->getNatureOfBusiness()) {
            $errors[] = [
                'natureOfBusiness' => [
                    'Nature of Business is required'
                ]
            ];
        }
        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }

    protected function validateRegOrLlp($command)
    {
        $errors = [];
        if (!$command->getCompanyNumber()) {
            $errors[] = [
                'companyNumber' => [
                    'Company Number is required'
                ]
            ];
        }
        if (!$command->getName()) {
            $errors[] = [
                'name' => [
                    'Operator Name is required'
                ]
            ];
        }
        if (!$command->getNatureOfBusiness()) {
            $errors[] = [
                'natureOfBusiness' => [
                    'Nature of Business is required'
                ]
            ];
        }
        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }

    protected function validateSoleTrader($command)
    {
        $errors = [];
        if (!$command->getNatureOfBusiness()) {
            $errors[] = [
                'natureOfBusiness' => [
                    'Nature of Business is required'
                ]
            ];
        }
        if (!$command->getLastName()) {
            $errors[] = [
                'lastName' => [
                    'Last name is required'
                ]
            ];
        }
        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }

    protected function validateIrfo($command)
    {
        $errors = [];
        if (!$command->getName()) {
            $errors[] = [
                'name' => [
                    'Operator Name is required'
                ]
            ];
        }
        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }
}

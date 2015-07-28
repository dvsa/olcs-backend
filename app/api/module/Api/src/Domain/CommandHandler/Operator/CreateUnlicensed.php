<?php

/**
 * Create Unlicensed Operator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Operator;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Unlicensed Operator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateUnlicensed extends AbstractCommandHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $organisation = $this->getOrganisation($command);

        $contactDetails = $this->getContactDetails($command);

        $licence = $this->getLicence($command, $organisation, $contactDetails);

        // save the licence; organisation, contactDetails and subsequent children will cascade persist
        $this->getRepo()->save($licence);

        $result
            ->addId('licence', $licence->getId())
            ->addMessage('Licence added')
            ->addId('organisation', $licence->getOrganisation()->getId())
            ->addMessage('Organisation added')
            ->addId('contactDetails', $licence->getCorrespondenceCd()->getId())
            ->addMessage('ContactDetails added');

        if (!empty($licence->getCorrespondenceCd()->getAddress())) {
            $result
                ->addId('address', $licence->getCorrespondenceCd()->getAddress()->getId())
                ->addMessage('Address added');
        }

        if (!empty($licence->getCorrespondenceCd()->getPhoneContacts())) {
            $pcIds = [];
            foreach ($licence->getCorrespondenceCd()->getPhoneContacts() as $pc) {
                $pcIds[] = $pc->getId();
            }
            if (!empty($pcIds)) {
                $result
                    ->addId('phoneContact', $pcIds)
                    ->addMessage('Phone contact(s) added');
            }
        }

        return $result;
    }

    /**
     * @param CommandInterface $command
     * @return OrganisationEntity
     */
    private function getOrganisation(CommandInterface $command)
    {
        $organisation = new OrganisationEntity();
        $organisation
            ->setName($command->getName())
            ->setIsUnlicensed(true)
            ->setType($this->getRepo()->getRefdataReference(OrganisationEntity::ORG_TYPE_OTHER));

        return $organisation;
    }

    /**
     * @param CommandInterface $command
     * @return ContactDetailsEntity
     */
    private function getContactDetails(CommandInterface $command)
    {
        return ContactDetailsEntity::create(
            $this->getRepo()->getRefdataReference(ContactDetailsEntity::CONTACT_TYPE_CORRESPONDENCE_ADDRESS),
            $this->getRepo('ContactDetails')->populateRefDataReference(
                $command->getContactDetails()
            )
        );
    }

    /**
     * @param CommandInterface $command
     * @param OrganisationEntity $organisation
     * @param ContactDetailsEntity $contactDetails
     * @return LicenceEntity
     */
    private function getLicence(
        CommandInterface $command,
        OrganisationEntity $organisation,
        ContactDetailsEntity $contactDetails
    ) {
        $licence = new LicenceEntity(
            $organisation,
            $this->getRepo()->getRefdataReference(LicenceEntity::LICENCE_STATUS_UNLICENSED)
        );

        $niFlag = $command->getTrafficArea() === TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE ? 'Y' : 'N';

        $licence
            ->setOrganisation($organisation)
            ->setTrafficArea($this->getRepo()->getReference(TrafficAreaEntity::class, $command->getTrafficArea()))
            ->setGoodsOrPsv($this->getRepo()->getRefdataReference($command->getOperatorType()))
            ->setLicenceType($this->getRepo()->getRefdataReference(LicenceEntity::LICENCE_TYPE_RESTRICTED))
            ->setNiFlag($niFlag)
            ->setCorrespondenceCd($contactDetails);

        return $licence;
    }
}
